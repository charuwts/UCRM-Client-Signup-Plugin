<?php
declare(strict_types=1);
namespace Ucsp;

class Interpreter {
  private static $whiteListedGet = ['service-plans' => [], 'countries' => ['second_level_ids' => ['states']]];
  private static $whiteListedPost = ['clients' => []];
  public static $dataUrl = null;

  public static function setDataUrl($dataUrl) {
    self::$dataUrl = $dataUrl;
  }

  public static function setFrontendKey($key) {
    if (!file_exists(self::$dataUrl.'frontendKey')) {
      file_put_contents(self::$dataUrl.'frontendKey', $key, LOCK_EX);
      return true;
    } else {
      return false;
    }
  }
  public static function getFrontendKey() {
    if (file_exists(self::$dataUrl.'frontendKey')) {
      return file_get_contents(self::$dataUrl.'frontendKey');
    } else {
      return false;
    }
  }

  private $response;
  private $code;
  private $ready = false;
  private $api;

  public function isReady() {
    return $this->ready;
  }
  public function getResponse() {
    return $this->response;
  }
  public function getCode() {
    return $this->code;
  }

  private static function parseAndValidateEndpoint($endpoint, $whitelist) {
    // # Remove backslash if at start of string
    $endpoint = ltrim($endpoint, '/');

    // # create array from URL
    $endpoint_array = explode('/', $endpoint);

    // # if first item is not in top level white list return false, else continue validation
    if (!array_key_exists($endpoint_array[0], $whitelist)) {
      return false;
    } else {

      // # if three levels deep continue validation
      if (count($endpoint_array) == 3) {
        
        // # If third level endpoint uses "second level ids" return true
        if (!empty($whitelist[$endpoint_array[0]]['second_level_ids'])) {
          return in_array($endpoint_array[2], $whitelist[$endpoint_array[0]]['second_level_ids']);

        // # If second level endpoint uses "third level ids" return true
        } elseif (!empty($whitelist[$endpoint_array[0]]['third_level_ids'])) {
          return in_array($endpoint_array[1], $whitelist[$endpoint_array[0]]['third_level_ids']);
        } else {
          return false;
        }

      // # if two levels deep continue validation
      } elseif (count($endpoint_array) == 2) {
        
        if (in_array($endpoint_array[1], $whitelist[$endpoint_array[0]])) {
          return true;
        } else {
          return false;
        }

      } elseif (count($endpoint_array) == 1) {
        return true;
      } else {
        // # fail validations by default, must be whitelisted and a specific level deep
        return false;
      }

    }
  }

  private static function validateGet($endpoint) {
    return self::parseAndValidateEndpoint($endpoint, self::$whiteListedGet);
  }

  private static function validatePost($endpoint) {
    return self::parseAndValidateEndpoint($endpoint, self::$whiteListedPost);
  }

  public function __construct() {
    $this->api = \Ubnt\UcrmPluginSdk\Service\UcrmApi::create();
    // $this->config = Config::create();
  }

  public function get($endpoint, $data) {
    if (self::validateGet($endpoint)) {
      return $this->api->get(
        $endpoint,
        $data
      );
    } else {
      throw new \UnexpectedValueException('{"code":404,"message":"No route GET: '.$endpoint.'"}', 404);
    }
  }

  public function post($endpoint, $data = []) {
    if (self::validatePost($endpoint)) {
      return $this->api->post(
        $endpoint,
        $data
      );
    } else {
      throw new \UnexpectedValueException('{"code":404,"message":"No route POST: '.$endpoint.'"}', 404);
    }
  }

  public function viewFile($endpoint) {
    $config = new Config();
    return $config->viewFile($endpoint);
  }

  public function updateFile($endpoint, $data) {
    $config = new Config();
    return $config->updateFile($endpoint, $data);
  }

  public function setResponse($response, $code = 200, $isReady = true) {
    $this->response = json_encode($response);
    $this->code = $code;
    $this->ready = $isReady;
  }

  public function run($payload) {

    if (!empty($payload)) {

      $payloadDecoded = json_decode($payload);

      if (!empty($payloadDecoded->frontendKey)) {

        if ($payloadDecoded->frontendKey != self::getFrontendKey()) {
          throw new \UnexpectedValueException('frontendKey is invalid', 400);
        }

        if (!empty($payloadDecoded->api)) {
          if (empty($payloadDecoded->api->endpoint)) {
            throw new \UnexpectedValueException('endpoint is not set', 400);
          }
          if (empty($payloadDecoded->api->type)) {
            throw new \UnexpectedValueException('type is not set', 400);
          }

          try {
            $data = empty($payloadDecoded->api->data) ? [] : (array)$payloadDecoded->api->data;
            if ($payloadDecoded->api->type == 'GET') {
              $response = $this->get($payloadDecoded->api->endpoint, $data);
            } elseif ($payloadDecoded->api->type == 'POST') {
              $response = $this->post($payloadDecoded->api->endpoint, $data);
            } elseif ($payloadDecoded->api->type == 'VIEW_FILE') {
              $response = $this->viewFile($payloadDecoded->api->endpoint);
            } elseif ($payloadDecoded->api->type == 'UPDATE_FILE') {
              $response = $this->updateFile($payloadDecoded->api->endpoint, $data);
            } else {
              throw new \UnexpectedValueException('type is invalid', 400);
            }
            
            $this->setResponse($response);
          } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->setResponse($e->getResponse()->getBody()->getContents(), $e->getCode());
          } catch (\IsNotAdminException $e) {
            $this->setResponse($e->getMessage(), $e->getCode());
          }
          // catch (\Exception $e) {
          //   $pluginLogManager = new \Ubnt\UcrmPluginSdk\Service\PluginLogManager();
          //   $pluginLogManager->appendLog($e->getMessage());
          //   $pluginLogManager->appendLog($e->getCode());
          // }

        } else {
          throw new \UnexpectedValueException('data is invalid', 400);
        }
      } elseif (!empty($payloadDecoded->uuid)) {
        try {
          file_put_contents(self::$dataUrl.'log.log', 'Webhook event received.', FILE_APPEND);
          $webhookHandler = new Webhook();
          $response = $webhookHandler->handleWebhook(json_encode($payloadDecoded));
          if ($response) {
            $this->setResponse('success');
          } else {
            $this->setResponse(null);
          }
        } catch (\WebhookException $e) {
          $this->setResponse($e->getMessage(), $e->getCode());
        }
      }

    }

  }

}