<?php 
/* 
 * Copyright © 2018 · Charuwts, LLC
 * All rights reserved.
 * You may not redistribute or modify the Software of Charuwts, LLC, and you are prohibited from misrepresenting the origin of the Software.
 * 
 */

class StripeApi {
  private $response;
  private static $endpoint_secret = null;
  private static $is_valid = false;
  public static $zero_decimal_currencies = ["mga", "bif", "clp", "pyg", "djf", "rwf", "gnf", "ugx", "jpy", "vnd", "vuv", "xaf", "kmf", "krw", "xof", "xpf"];
  // ## Currencies supported by UCRM but not supported by Stripe ["vef", "ghc", "tvd", "lvl", "trl", "ltl", "cup", "ggp", "imp", "jep", "syp", "irr", "omr", "byn", "kpw", "zwd"]

  public static function notZeroDecimal($currency) {
    if (in_array($currency, self::$zero_decimal_currencies)) {
      return false;
    } else {
      return true;
    }
  }

  public static function setEndpointSecret($secret='') {
    self::$endpoint_secret = $secret;
  }

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
   * # Catch Stripe exceptions when making API request
   *
   * @param arguments $args // args can be whatever the anonymous function needs, if multiple args are needed pass in an array
   * @param function $function
   *
   */
  public function StripeTry($args, $function) {
    try {
      return $function($args);
    } catch(\Stripe\Error\Card $e) {
      // $body = $e->getJsonBody();
      // $err  = $body['error'];
      log_event("Card declined", $e);
    } catch (\Stripe\Error\RateLimit $e) {
      log_event("Too many requests made to the API too quickly", $e);
    } catch (\Stripe\Error\InvalidRequest $e) {
      log_event("Invalid parameters were supplied to Stripe's API", $e);
    } catch (\Stripe\Error\Authentication $e) {
      log_event("Authentication with Stripe's API failed", $e);
    } catch (\Stripe\Error\ApiConnection $e) {
      log_event("Network communication with Stripe failed", $e);
    } catch (\Stripe\Error\Base $e) {
      log_event("Stripe Error", $e);
    } catch (Exception $e) {
      log_event("Stripe Exception", $e);
    }
  }

  /**
   * # Custom Payload Verification
   *
   * @param object $payload_decoded
   * @param string $pluginAppKey
   *
   */
  protected function validateForm($payload_decoded, $pluginAppKey) {
    try {
      foreach($payload_decoded->stripeInfo as $key => $value) {
        if (empty($value) && ($value != 0)) {
          throw new UnexpectedValueException("{$key} cannot be empty");   
        } 
      }
      
      foreach($payload_decoded->client as $key => $value) {
        if (empty($value) && ($value != 0)) {
          throw new UnexpectedValueException("{$key} cannot be empty");   
        } 
      }
      // if (empty($payload_decoded->client->username)) {
      //   throw new UnexpectedValueException("email cannot be empty");   
      // }
      // if (empty($payload_decoded->service->activeFrom)) {
      //   throw new UnexpectedValueException("invalidData activeFrom");   
      // }
      // if (empty($payload_decoded->service->invoicingStart)) {
      //   throw new UnexpectedValueException("invalidData invoicingStart");
      // }
      // if (empty($payload_decoded->service->invoicingPeriodStartDay)) {
      //   throw new UnexpectedValueException("invalidData invoicingPeriodStartDay");
      // }
      if (empty($payload_decoded->service->servicePlanId)) {
        throw new UnexpectedValueException("invalidData servicePlanId");
      }
      if (empty($payload_decoded->service->servicePlanPeriodId)) {
        throw new UnexpectedValueException("invalidData servicePlanPeriodId");
      }

      if ($pluginAppKey != FRONTEND_PUBLIC_KEY) {
        throw new UnexpectedValueException("Invalid pluginAppKey");
      }

    } catch(\UnexpectedValueException $e) {
      if (strpos( $e->getMessage(), "invalidData" ) !== false) {
        $errors = array('code' => 422, 'message' => 'Invalid Data', 'redirect' => true);
      } else {
        $errors = array('code' => 422, 'message' => 'Invalid Info', 'invalidInfo' => $e->getMessage());
      }
      echo json_response(json_encode($errors), 422, true);
      exit();
    }
  }

  /**
   * ## Stripe Webhook Validation
   * # https://stripe.com/docs/webhooks/signatures
   * 
   * @param string $payload // JSON
   *
   * Exits upon failure
   *
   * @return boolean // true
   */
  protected function validateWebhook($payload) {
    $event = null;
    $sig_header = $_SERVER["HTTP_STRIPE_SIGNATURE"];
    try {
      $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, self::$endpoint_secret
      );
    } catch(\UnexpectedValueException $e) {
      log_event('Stripe error', 'invalid payload', 'error'); // Invalid payload
      exit();
    } catch(\Stripe\Error\SignatureVerification $e) {
      $this->setResponse($e->getMessage(), 400);
      log_event('Stripe error', 'invalid signature', 'error'); // Invalid signature
      exit();
    }
    return true;
  }





}