<?php
/* 
 * Copyright © 2018 · Charuwts, LLC
 * All rights reserved.
 * You may not redistribute or modify the Software of Charuwts, LLC, and you are prohibited from misrepresenting the origin of the Software.
 * 
 */

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
  
  // ## Else, return form
} else {
  UsageHandler::validate();
  ?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo !empty(FORM_TITLE) ? FORM_TITLE.' - ' : ''; ?> Signup</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="ucrm-client-signup-form/config/environment" content="%7B%22modulePrefix%22%3A%22ucrm-client-signup-form%22%2C%22environment%22%3A%22production%22%2C%22rootURL%22%3A%22/%22%2C%22locationType%22%3A%22none%22%2C%22EmberENV%22%3A%7B%22FEATURES%22%3A%7B%7D%2C%22EXTEND_PROTOTYPES%22%3A%7B%22Date%22%3Afalse%7D%7D%2C%22APP%22%3A%7B%22rootElement%22%3A%22%23ember-signup%22%2C%22host%22%3A%22<?php echo rawurlencode((string)PLUGIN_PUBLIC_URL); ?>%22%2C%22completionText%22%3A%22<?php echo rawurlencode((string)COMPLETION_TEXT); ?>%22%2C%22pluginAppKey%22%3A%22<?php echo FRONTEND_PUBLIC_KEY; ?>%22%2C%22name%22%3A%22ucrm-client-signup-form%22%2C%22version%22%3A%221.0.0+84bc7820%22%7D%2C%22stripe%22%3A%7B%22publishableKey%22%3A%22<?php echo STRIPE_PUBLIC_KEY; ?>%22%7D%2C%22exportApplicationGlobal%22%3Afalse%7D" />

    <style type="text/css">
      <?php // ## UCRM requires file paths, Using PHP include instead of HTML tags to avoid relative URL ?>
      <?php include(PROJECT_PATH.'/assets/vendor-463d4d71894dfde19d720aa6b937502f.css'); ?>
      <?php include(PROJECT_PATH.'/assets/ucrm-client-signup-form-500a5c0e9df67704f365edc02f483591.css'); ?>
    </style>
    
  </head>
  <body>
    <script type="text/javascript" src="https://js.stripe.com/v3/"></script>

    <?php if (!empty(LOGO_URL)) { ?>
      <img src="<?php echo LOGO_URL; ?>" class="logo">
    <?php } ?>

    <?php if (!empty(FORM_TITLE)) { ?>
      <h1 class="text-center mt-2"><?php echo FORM_TITLE; ?></h1>
    <?php } ?>

    <br clear="all">
    <?php if (!empty(FORM_DESCRIPTION)) { ?>
      <div class="form-description">
        <?php echo FORM_DESCRIPTION; ?>
      </div>
      <br clear="all">
    <?php } ?>
    
    <div id="ember-signup"></div>
    <script type="text/javascript">
      <?php // ## UCRM requires file paths, Using PHP include instead of HTML tags to avoid relative URL ?>
      <?php include(PROJECT_PATH.'/assets/vendor-9bfe2b44f19210a7c1959ef10ea382e2.js'); ?>
      <?php include(PROJECT_PATH.'/assets/ucrm-client-signup-form-7e52ddab4394613d13d622b4c72e4aa7.js'); ?>
    </script>

    <div id="ember-bootstrap-wormhole"></div>
  </body>
</html>

<?php
}
?>