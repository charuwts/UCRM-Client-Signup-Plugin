<?php
declare(strict_types=1);
namespace Ucsp;

chdir(__DIR__);
define("WEBHOOK_HANDLER_PATH", __DIR__);
require_once(WEBHOOK_HANDLER_PATH.'/../includes/custom-exceptions.php');

class Webhook extends Generator {
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
    // # Get array from payload
    $payload_decoded = json_decode($payload, true);
    
    // # Double check webhook exists in UCRM
    $isValid = $this->validateWebhook($payload_decoded['uuid']);

    if ($isValid) {
      // # If event name is client.add
      if ($payload_decoded['eventName'] == 'client.add' && !empty($payload_decoded['extraData']['entity']['attributes'])) {
        //# Check for services
        $service_details = [];
        foreach ($payload_decoded['extraData']['entity']['attributes'] as $attr) {
          if ($attr['key'] == 'ucspServiceData') {
            $service_details = explode(",", $attr['value']);
          }
        }
        if (count($service_details) > 0) {
          $user_id = $payload_decoded['entityId'];
          try {
            // ## Set active from and invoicing start to +1 month to give time to adjust service invoice dates manually
            $date = new \DateTime();
            $date = date('c', strtotime("-1 days"));
            // $date = date('c', strtotime("+1 months"));

            $webhook_event = $this->post('clients/'.$user_id.'/services', [
              "servicePlanId" => (int)$service_details[0],
              "servicePlanPeriodId" => (int)$service_details[1],
              "activeFrom" => $date,
              "invoicingStart" => $date
            ]);
            return true;
          } catch (\GuzzleHttp\Exception\ClientException $e) {
            file_put_contents(Interpreter::$dataUrl.'log.log', $e->getMessage(), FILE_APPEND);
            return $e;
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