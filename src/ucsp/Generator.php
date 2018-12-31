<?php
declare(strict_types=1);
namespace Ucsp;

chdir(__DIR__);
define("GENERATOR_PATH", __DIR__);
require_once(CONFIG_PATH.'/../includes/custom-exceptions.php');

class Generator {
  public function __construct() {
    $this->api = \Ubnt\UcrmPluginSdk\Service\UcrmApi::create();
    // $this->config = Config::create();
  }

  public function addStripeUserToClient {
  }
}