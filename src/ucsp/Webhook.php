<?php
declare(strict_types=1);
namespace Ucsp;

chdir(__DIR__);
define("WEBHOOK_HANDLER_PATH", __DIR__);
require_once(WEBHOOK_HANDLER_PATH.'/../includes/custom-exceptions.php');

class Webhook extends Generator {
  public function __construct() {
    $this->api = \Ubnt\UcrmPluginSdk\Service\UcrmApi::create();
  }

  public function get($endpoint, $data = []) {
    return $this->api->get($endpoint, $data);
  }

  public function post($endpoint, $data = []) {
    return $this->api->post($endpoint, $data);
  }

  public function patch($endpoint, $data = []) {
    return $this->api->patch($endpoint, $data);
  }

  public function handleToken($client_id, $token, $email) {
    $stripe_handler = new Stripe();
    $stripe_handler->createCustomer($client_id, $token, $email);
  }

  public function validateWebhook($uuid) {
    // # If uuid exists
    if ($uuid) {
      
      try {
        $webhook_event = $this->get('webhook-events/'.$uuid);
      } finally {
        // # Always return either true or false
        if (!empty($webhook_event['uuid'])) {
          return $webhook_event['uuid'] == $uuid;
        } else {
          return false;
        }
      }
      
    // # ... else return false
    } else {
      return false;
    }
  }
  public function handleWebhook($payload) {

    $configManager = \Ubnt\UcrmPluginSdk\Service\PluginConfigManager::create();
    $config = $configManager->loadConfig();


    // # Get array from payload
    $payload_decoded = json_decode($payload, true);
    
    // # Double check webhook exists in UCRM
    $isValid = $this->validateWebhook($payload_decoded['uuid']);

    if ($isValid) {
      // # If event name is client.add
      if ($payload_decoded['eventName'] == 'client.add' && !empty($payload_decoded['extraData']['entity']['attributes'])) {

        $client = $payload_decoded['extraData']['entity'];

        if ($config["ADMIN_TICKET"]) {

          $this->post('ticketing/tickets', [
            "subject" => "New Client Signup",
            "clientId" => $client['id'],
            'status' => 0,
            'public' => false,
            'activity' => [
              [
                'public' => false,
                'comment' => [
                  'body' => "Client created via Signup Form.",
                ]
              ]
            ]
          ]);

        }
        
        if ($config["INVITE"]) {
          $this->patch('clients/'.$client['id'].'/send-invitation');
        }

        //# Check for services
        $service_details = [];
        foreach ($payload_decoded['extraData']['entity']['attributes'] as $attr) {
          if ($attr['key'] == 'ucspServiceData') {
            if (!empty($attr)) {
              $service_details = explode(",", $attr['value']);
            }
          }
          if ($attr['key'] == 'ucspGatewayToken') {
            if (!empty($payload_decoded['extraData']['entity']['contacts'])) {
              $this->handleToken($payload_decoded['entityId'], $attr['value'], $payload_decoded['extraData']['entity']['contacts'][0]['email']);
            }
          }
        }
        if (count($service_details) > 0) {
          $user_id = $payload_decoded['entityId'];
          if (($service_details[0] !== null) && ($service_details[0] !== 'null')) {

            try {
              // ## Set active from and invoicing start to +1 month to give time to adjust service invoice dates manually
              $date = new \DateTime();
              // $date = date('c', strtotime("-1 days"));
              $date = date('c', strtotime("+1 months"));

              $user = $this->get('clients/'.$user_id);
              $isQuoted = $user['isLead'] ? true : false;

              $webhook_event = $this->post('clients/'.$user_id.'/services', [
                "servicePlanId" => (int)$service_details[0],
                "servicePlanPeriodId" => (int)$service_details[1],
                "activeFrom" => $date,
                "invoicingStart" => $date,
                "isQuoted" => $isQuoted,
              ]);
              return true;
            } catch (\GuzzleHttp\Exception\ClientException $e) {
              file_put_contents(Interpreter::$dataUrl.'log.log', $e->getMessage(), FILE_APPEND);
              return $e;
            }

          }
        } else {
          return false;
        }
      }
    } else {
      return false;
    }
  }
}