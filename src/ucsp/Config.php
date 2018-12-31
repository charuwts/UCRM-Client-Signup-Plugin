<?php
declare(strict_types=1);
namespace Ucsp;

chdir(__DIR__);
define("CONFIG_PATH", __DIR__);
require_once(CONFIG_PATH.'/../includes/custom-exceptions.php');

class Config {
  private $accessGranted = false;
  private $api;
  private $whiteListViews = ['service-filters' => true, 'translations' => true, 'current-translation' => true];

  private function canViewEndpoint($endpoint) {
    return array_key_exists($endpoint, $this->whiteListViews);
  }

  private function grantAccess() {
    $this->accessGranted = true;
  }

  public function isAccessGranted() {
    return $this->accessGranted;
  }
  
  public function hasPermission() {
    $ucrmSecurity = \Ubnt\UcrmPluginSdk\Service\UcrmSecurity::create();
    $user = $ucrmSecurity->getUser();
    if ( $user && $user->hasViewPermission(\Ubnt\UcrmPluginSdk\Security\PermissionNames::SYSTEM_PLUGINS)) {
      return true;
    } else {
      return false;
    }
  }

  public function checkPermissions() {
    try {
      if ( $this->hasPermission() ) {
        $this->api = \Ubnt\UcrmPluginSdk\Service\UcrmApi::create();
        $this->grantAccess();
      } else {
        throw new \IsNotAdminException('You do not have permission to access this config', 403);
      }
    } catch (\Exception $e) {
      throw new \IsNotAdminException('You do not have permission to access this config', 403);
    }
  }

  public function __destruct() {
      unset($this->api);
      $this->accessGranted = false;
  }

  public function writeToFile($filename, $data) {
    if ($this->isAccessGranted()) {
      $config_json_file = CONFIG_PATH.'/../data/'.$filename.'.json'; 
      file_put_contents($config_json_file, json_encode($data));
      
      if (file_exists($config_json_file)) {
        return true;
      } else {
        return false;
      }
    } else {
      throw new \ConfigException('Permission Denied', 403);
    }
  }

  public function updateFile($endpoint, $data = []) {
    $this->checkPermissions();
    $wasCreated = $this->writeToFile($endpoint, $data);
    if ($wasCreated) {
      return $this->viewFile($endpoint);
    } else {
      throw new \ConfigException('failed to update');
    }
  }

  public function viewFile($endpoint) {
    if ($this->canViewEndpoint($endpoint)) {
      $config_json_file = CONFIG_PATH.'/../data/'.$endpoint.'.json'; 
      $public_json_file = CONFIG_PATH.'/../public/'.$endpoint.'.json'; 

      if (file_exists($config_json_file)) {
        $jsonString = file_get_contents($config_json_file);
        $contents = empty($jsonString) ? [] : json_decode($jsonString, true);
      } elseif (file_exists($public_json_file)) {
        $jsonString = file_get_contents($public_json_file);
        $contents = empty($jsonString) ? [] : json_decode($jsonString, true);
      } else {
        $contents = [];
      }
      return $contents;
    } else {
      throw new \ConfigException('Permission Denied', 403);
    }
  }



}
