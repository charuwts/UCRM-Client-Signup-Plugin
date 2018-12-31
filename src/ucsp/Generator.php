<?php
declare(strict_types=1);
namespace Ucsp;

chdir(__DIR__);
define("GENERATOR_PATH", __DIR__);
require_once(GENERATOR_PATH.'/../includes/custom-exceptions.php');

class Generator {
  private $UscpTokens = ['UcspStripeUserId', 'UcspFormEmail', 'UcspFormStep', 'UcspErrors'];

  public function __construct() {
    // $this->api = new Interpreter();
    $this->api = \Ubnt\UcrmPluginSdk\Service\UcrmApi::create();

    // $this->config = Config::create();
  }

  public function get($endpoint) {
    return $this->api->get('custom-attributes');
  }

  public function customAttributesExists($attrId) {
    $results = $this->get('custom-attributes');

    if (empty($results) || is_null($results)) {
      return false;
    } else {
      $keys = array_map(function($i) {
        return $i['key'];
      }, $results);

      $remainder = count(array_diff($this->UscpTokens, $keys));
      
      if ($remainder > 0) {
        return false;
      } else {
        return true;
      }

    }
  }  
}