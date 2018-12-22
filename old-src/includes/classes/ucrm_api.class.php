<?php 
/* 
 * Copyright © 2018 · Charuwts, LLC
 * All rights reserved.
 * You may not redistribute or modify the Software of Charuwts, LLC, and you are prohibited from misrepresenting the origin of the Software.
 * 
 */
namespace UCSP;

class UcrmApi {
  // ### Class Properties
  private $response;

  /**
   * # Static Properties
   *
   */
  public static $ucrm_api_url;
  public static $ucrm_key;

  /**
   * # Public Setters
   *
   * @param string self::$ucrm_key
   * @param string self::$ucrm_api_url
   *
   */
  public static function setUcrmKey($value='')    { self::$ucrm_key     = $value; }
  public static function setUcrmApiUrl($value='') { self::$ucrm_api_url = $value; }

  /**
   * # Get UCRM response
   *
   * @param string $this->response
   *
   */
  public function getResponse() {
    return $this->response;
  }
    
  /**
   * # Set UCRM response
   *
   * @param string $message
   *
   */
  protected function setResponse($message) {
    $this->response = $message;
  }

  /**
   * # VALIDATE PAYLOAD OBJECTS
   *
   * @param array $object
   * @param boolean $requireKey
   *
   * @return boolean
   */
  protected function validateObject($object, $requireKey=false) {
    try {
      $errors = [];
      foreach($object as $key => $value) {
        if (empty($value) && ($value !== 0)) {
          $errors[$key] = "cannot be empty";
        } 
      }
      if (!empty($errors)) {
        $resp = ["code" => 422, "message" => "Validation failed.", "errors" => $errors ];
        throw new UnexpectedValueException(json_encode($resp));   
      }

      if ($requireKey) {
        if ($object->pluginAppKey != FRONTEND_PUBLIC_KEY) {
          throw new UnexpectedValueException("Invalid pluginAppKey");   
        }
      }

    } catch(\UnexpectedValueException $e) {
      log_event('UCRM exception', $e->getMessage(), 'error');
      echo json_response($e->getMessage(), 422, true);
      exit();
    }
    return true;
  }



}