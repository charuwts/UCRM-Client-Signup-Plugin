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
  $handler = new \UCSP\StripeHandler;
  $handler->handleWebhook($payload);
  echo json_response($handler->getResponse(), 200, true);
  exit();
}

try {
  
  // ## If payload has servicePlans - servicePlans == true
  if (!empty($payload_decoded->servicePlans)) {
    // ## Return service plans
    echo \UCSP\UcrmHandler::getServicePlans();
    exit();
    // ## If payload has servicePlanFilters - servicePlanFilters == true
  } elseif (!empty($payload_decoded->servicePlanFilters)) {
    // ## Return service plans filtered
    echo \UCSP\UcrmHandler::getServicePlanFilters();
    exit();

    // ## If payload has createServicePlanFilters - createServicePlanFilters == true
  } elseif (!empty($payload_decoded->createServicePlanFilters)) {
    // ## Return service plans filtered
    echo \UCSP\UcrmHandler::createServicePlanFilters();
    exit();
  } elseif (!empty($payload_decoded->updateServicePlanFilters)) {
    // ## Return service plans filtered
    echo \UCSP\UcrmHandler::updateServicePlanFilters(json_encode($payload_decoded->updateServicePlanFilters));
    exit();
  }

} catch (\UCSP\ApiException $e) {
  echo json_response($e->getMessage(), $e->getCode());
  exit();
} catch (\Exception $e) {
  echo json_response($e->getMessage(), $e->getCode());
  exit();
}
// ## If payload has country_id
if (!empty($payload_decoded->country_id)) {
    // ## Instantiate handler
    $handler = new \UCSP\UcrmHandler;
    // ## Return countries
    echo json_response($handler->getStatesByCountry($payload_decoded->country_id), 200, true);

  // ## If payload has countries - countries == true
} elseif (!empty($payload_decoded->countries)) {
    // ## Instantiate handler
    $handler = new \UCSP\UcrmHandler;
    // ## Return countries
    echo json_response($handler->getCountries(), 200, true);
    
// ## stripe info and appkey
} elseif (!empty($payload_decoded->pluginAppKey) && !empty($payload_decoded->stripeInfo)) {
  // ## Instantiate handler
  $handler = new \UCSP\StripeHandler;
  // ## Attempt to build Services
  $handler->buildServices($payload);
  echo json_response($handler->getResponse(), 200);
  
// ## build services and appkey
} elseif (!empty($payload_decoded->pluginAppKey) && !empty($payload_decoded->buildServices)) {
  // ## Instantiate handler
  $handler = new \UCSP\UcrmHandler;
  
  // ## Attempt to build Services
  $handler->buildServices($payload);
  echo json_response($handler->getResponse(), 200);
  
} elseif (!empty($_GET['admin'])) {
  
  if ($_GET['admin'] == 'services') {
    include(PROJECT_PATH."/includes/pages/admin.php");
  }

// ## Else, return form  
} else {
  include(PROJECT_PATH."/includes/pages/signup.php");
}
?>