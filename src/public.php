<?php
/* 
 * Copyright © 2018 · Charuwts, LLC
 * All rights reserved.
 * You may not redistribute or modify the Software of Charuwts, LLC, and you are prohibited from misrepresenting the origin of the Software.
 * 
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');


chdir(__DIR__);

define("PROJECT_PATH", __DIR__);

require_once(PROJECT_PATH.'/includes/initialize.php');

// ## Get JSON from post request
$payload = @file_get_contents("php://input");
$payload_decoded = json_decode($payload);

if (!empty($_SERVER["HTTP_STRIPE_SIGNATURE"])) {
  $handler = new StripeHandler;
  $handler->handleWebhook($payload);
  echo json_response($handler->getResponse(), 200, true);

  // ## If payload has servicePlans - servicePlans == true
} elseif (!empty($payload_decoded->servicePlans)) {
    // ## Instantiate handler
    $handler = new UcrmHandler;
    // ## Return service plans
    echo json_response($handler->getServicePlans(), 200, true);

  // ## If payload has country_id
} elseif (!empty($payload_decoded->country_id)) {
    // ## Instantiate handler
    $handler = new UcrmHandler;
    // ## Return countries
    echo json_response($handler->getStatesByCountry($payload_decoded->country_id), 200, true);

  // ## If payload has countries - countries == true
} elseif (!empty($payload_decoded->countries)) {
    // ## Instantiate handler
    $handler = new UcrmHandler;
    // ## Return countries
    echo json_response($handler->getCountries(), 200, true);
    
// ## Only run if app key exists
} elseif (!empty($payload_decoded->pluginAppKey)) {
  // ## Instantiate handler
  $handler = new StripeHandler;
  // ## Attempt to build Services
  $handler->buildServices($payload);
  echo json_response($handler->getResponse(), 200);
  
} elseif (!empty($_GET['admin'])) {
  
  if ($_GET['admin'] == 'stripe-info') {
    include(PROJECT_PATH."/includes/pages/services.php");
  }

// ## Else, return form  
} else {
  include(PROJECT_PATH."/includes/pages/signup.php");
}
?>