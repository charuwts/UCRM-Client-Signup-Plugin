<?php
declare(strict_types=1);
namespace Ucsp;

chdir(__DIR__);
define("GENERATOR_PATH", __DIR__);
require_once(GENERATOR_PATH.'/../includes/custom-exceptions.php');

class Generator {
  private $UscpCustomAttributes = ['Ucsp Stripe Customer Id' => 'UcspStripeCustomerId', 'Ucsp Form Email' => 'UcspFormEmail', 'Ucsp Form Step' => 'UcspFormStep', 'Ucsp Errors' => 'UcspErrors'];

  public function __construct() {
    $this->log = new \Ubnt\UcrmPluginSdk\Service\PluginLogManager();
    $this->api = \Ubnt\UcrmPluginSdk\Service\UcrmApi::create();
  }

  public function get($endpoint) {
    return $this->api->get($endpoint);
  }

  public function post($endpoint, $data = []) {
    return $this->api->post($endpoint, $data);
  }

  public function customAttributesExists() {
    // # Get attributes from UCRM
    $results = $this->get('custom-attributes');

    // # Make sure empty or null is set to an empty array
    if (empty($results) || is_null($results)) {
      $results = [];
    }

    // # Retrieve only keys from attributes and assign to new array
    $keys = array_map(function($i) {
      if ($i['attributeType'] == 'client') {
        return $i['key'];
      }
    }, $results);

    // # Check against required attributes and return any that do not match/exist in UCRM
    $remainder = array_diff($this->UscpCustomAttributes, $keys);
    
    // # If any remain return them...
    if (count($remainder) > 0) {
      return $remainder;
    } else {
      // # ...else custom attributes exist return true
      return true;
    }

  }

  public function createCustomAttributes() {

    // # Do not create attributes if they already exist
    if ($this->customAttributesExists() == true) {
      return false;
    } else {
      // # Otherwise, get missing attributes...
      $missingAttributes = $this->customAttributesExists();
      
      // # ...and generate them
      foreach ($missingAttributes as $key => $value) {
        $results = $this->post('custom-attributes', ['name' => $key, 'attributeType' => 'client']);
      }

      // # They should exist now...
      if ($this->customAttributesExists() == true) {
        return $results;
      } else {
        // # ...Log error if they don't
        $message = 'failed to create custom attributes '.$results;
        $this->log->appendLog($message);
      }

    }
  }
}