<?php
/* 
 * Copyright © 2018 · Charuwts, LLC
 * All rights reserved.
 * You may not redistribute or modify the Software of Charuwts, LLC, and you are prohibited from misrepresenting the origin of the Software.
 * 
 */

class UsageHandler {

  /**
   * # Handle Guzzle Exception and exit
   *
   * @param array $e
   * @param boolean $log
   *
   * @return exit();
   */
  protected static function handleGuzzleException($e, $log = false, $endpoint='') {
    // # Get json response
    $body = $e->getResponse()->getBody();

    // # Get get code from response
    $json_decoded = json_decode($body);
    $code = $json_decoded->code;
    // # Send response and exit
    if ($log) {
      log_event('Exception', "{$body}: {$code} - Endpoint: {$endpoint}", 'error');
    }
    echo json_response($body, $code, true);
    exit();
  }

  /**
   * # Setup Guzzle for UCRM
   *
   * @param string $method // "GET", "POST", "PATCH"
   * @param string $endpoint
   * @param array  $content
   *
   * @return array
   */
  public static function guzzle(
    $method, 
    $endpoint,
    array $content = []
  ) {    
    // log_event('method', $method, 'test');
    // log_event('endpoint', $endpoint, 'test');
    // log_event('content', print_r($content, true), 'test');
    try {
      $client = new \GuzzleHttp\Client([
        'headers' => ['X-Auth-App-Key' => UcrmApi::$ucrm_key],
      ]);

      if ($endpoint === 'CHARUWTS_SIGNUPS') {
        $url = API_URL.'/signups';
      } elseif ($endpoint === 'CHARUWTS_INVOICES') {
        $url = API_URL.'/invoices';
      } elseif ($endpoint === 'CHARUWTS_VALIDATION') {
        $url = API_URL.'/validate';
      } else {
        $url = UcrmApi::$ucrm_api_url.$endpoint;
      }

      $res = $client->request($method, $url, ['json' => $content]);
      $code = $res->getStatusCode();
      $body = (string)$res->getBody();
      // log_event('body', print_r($body, true), 'test');

      return ["status" => $code, "message" => $body];
    } catch (\GuzzleHttp\Exception\ClientException $e) {
      self::handleGuzzleException($e);
    } catch (\GuzzleHttp\Exception\ServerException $e) {
      self::handleGuzzleException($e, true, $endpoint);
    }
  }


  /**
   * # Increment Subscription Signup Meter
   *
   * @param string $client_id
   * @param string $service_id
   *
   */
  public static function is($client_id, $service_id) {
    $http_host = null;
    $server_addr = null;

    if (!empty($_SERVER['HTTP_HOST'])) { $http_host = $_SERVER['HTTP_HOST']; }
    if (!empty($_SERVER['SERVER_ADDR'])) { $server_addr = $_SERVER['SERVER_ADDR']; }

    $content = [
      "type" => "signup",
      "client_id" => $client_id,
      "service_id" => $service_id,
      "subscription_id" => \UCSP\Config::$PLUGIN_SUBSCRIPTION_ID,
      "plugin_unique_key" => \UCSP\Config::$PLUGIN_UNIQUE_KEY,
      "domain" => \UCSP\Config::$PLUGIN_DOMAIN,
      "http_host" => $http_host,
      "server_addr" => $server_addr,
    ];

    $response = self::guzzle('POST', 'CHARUWTS_SIGNUPS', $content);
    if ($response['message'] !== 'SIGNUPS_INCREMENTED') {
      file_put_contents(PROJECT_PATH.'/data/plugin.log', '', LOCK_EX);
      log_event('CONFIGURATION ERROR', $response['message'], 'error');
      exit();
    }
  }

  /**
   * # Increment Subscription Invoices Meter
   *
   * @param array $invoice_ids
   * @param integer $service_id
   *
   * @return array
   */
  public static function ii($invoice_ids, $count) {
    $content = [
      "type" => "invoice",
      "invoice_ids" => $invoice_ids,
      "count" => $count,
      "subscription_id" => \UCSP\Config::$PLUGIN_SUBSCRIPTION_ID,
      "plugin_unique_key" => \UCSP\Config::$PLUGIN_UNIQUE_KEY,
      "domain" => \UCSP\Config::$PLUGIN_DOMAIN,
    ];

    $response = self::guzzle('POST', 'CHARUWTS_INVOICES', $content);
    if ($response['message'] !== 'INVOICES_INCREMENTED') {
      file_put_contents(PROJECT_PATH.'/data/plugin.log', '', LOCK_EX);
      log_event('CONFIGURATION ERROR', $response['message'], 'error');
      exit();
    }
  }

  /**
   * # Setup Guzzle for UCRM
   *
   *
   * @return array
   */
  public static function validate() {
    $http_host = null;
    $server_addr = null;
    if (!empty($_SERVER['HTTP_HOST'])) { $http_host = $_SERVER['HTTP_HOST']; }
    if (!empty($_SERVER['SERVER_ADDR'])) { $server_addr = $_SERVER['SERVER_ADDR']; }

    $content = [
      "type" => "validate",
      "subscription_id" => \UCSP\Config::$PLUGIN_SUBSCRIPTION_ID,
      "plugin_unique_key" => \UCSP\Config::$PLUGIN_UNIQUE_KEY,
      "domain" => \UCSP\Config::$PLUGIN_DOMAIN,
      "http_host" => $http_host,
      "server_addr" => $server_addr,
    ];

    $response = self::guzzle('POST', 'CHARUWTS_VALIDATION', $content);

    if ($response['message'] !== 'KEYS_ARE_VALID') {
      file_put_contents(PROJECT_PATH.'/data/plugin.log', '', LOCK_EX);
      log_event('CONFIGURATION ERROR', $response['message'], 'error');
      echo 'An application error has occurred, check logs or contact administrator';
      exit();
    }
  }
}
