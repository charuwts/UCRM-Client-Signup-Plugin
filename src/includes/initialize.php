<?php
/* 
 * Copyright © 2018 · Charuwts, LLC
 * All rights reserved.
 * You may not redistribute or modify the Software of Charuwts, LLC, and you are prohibited from misrepresenting the origin of the Software.
 * 
 */
define("API_URL", 'https://api.charuwts.com/api/v1/subscriptions');

// ## Project paths
define("PROJECT_SRC_PATH", PROJECT_PATH . '/includes');
define("CLASSES_PATH", PROJECT_PATH . '/includes/classes');

// ## Setup Environment Constants
$ucrm_string = file_get_contents(PROJECT_PATH."/ucrm.json");
$ucrm_json = json_decode($ucrm_string);
define("UCRM_PUBLIC_URL", $ucrm_json->ucrmPublicUrl);
define("UCRM_KEY", $ucrm_json->pluginAppKey);
define("PLUGIN_PUBLIC_URL", $ucrm_json->pluginPublicUrl);
define("UCRM_API_URL", UCRM_PUBLIC_URL.'api/v2.9');

$config_path = PROJECT_PATH."/data/config.json";

// ## Just a unique key to give to ember for extra security when making requests
// $key = password_hash(PLUGIN_PUBLIC_URL, PASSWORD_DEFAULT);
// define("FRONTEND_PUBLIC_KEY", $key);
define("FRONTEND_PUBLIC_KEY", 'development_key');

// ## Setup user configuration settings, if they exist
if (file_exists($config_path)) {

  $config_string = file_get_contents($config_path);
  $config_json = json_decode($config_string);

  define("CUSTOM_ATTRIBUTE_ID", $config_json->requiredCustomAttributeId);
  define("PLUGIN_SUBSCRIPTION_ID", $config_json->requiredPluginSubscriptionId);
  define("PLUGIN_UNIQUE_KEY", $config_json->requiredPluginUniqueKey);
  define("PLUGIN_DOMAIN", $config_json->requiredPluginDomain);


  // ## Check if Stripe configuration is set, if so USE_STRIPE=true
  if (!empty($config_json->optionalStripeSecretKey) && !empty($config_json->optionalStripePublicKey) && !empty($config_json->optionalStripeEndpointSecret)) {
    define("USE_STRIPE", true);
    define("STRIPE_SECRET_KEY", $config_json->optionalStripeSecretKey);
    define("STRIPE_PUBLIC_KEY", $config_json->optionalStripePublicKey);
    define("STRIPE_ENDPOINT_SECRET", $config_json->optionalStripeEndpointSecret);
  } else {
    define("USE_STRIPE", false);
  }

  if (!empty($config_json->optionalLogoUrl)) {
    define("LOGO_URL", $config_json->optionalLogoUrl);
  } else {
    define("LOGO_URL", null);
  }
  if (!empty($config_json->optionalFormTitle)) {
    define("FORM_TITLE", $config_json->optionalFormTitle);
  } else {
    define("FORM_TITLE", null);
  }
  if (!empty($config_json->optionalFormDescription)) {
    define("FORM_DESCRIPTION", $config_json->optionalFormDescription);
  } else {
    define("FORM_DESCRIPTION", null);
  }
  if (!empty($config_json->optionalCompletionText)) {
    define("COMPLETION_TEXT", $config_json->optionalCompletionText);
  } else {
    define("COMPLETION_TEXT", 'Thank you for signing up! You will receive an invitation to access your account upon approval.');
  }
  if (!empty($config_json->optionalCountrySelect)) {
    if ($config_json->optionalCountrySelect === 'TRUE') {
      define("USE_COUNTRY_SELECT", 'TRUE');
    } else {
      define("USE_COUNTRY_SELECT", 'FALSE');
    }
  } else {
    define("USE_COUNTRY_SELECT", 'FALSE');
  }
  
} else {
  define("FORM_TITLE", null);
  define("LOGO_URL", null);
  define("FORM_DESCRIPTION", null);
  define("COMPLETION_TEXT", 'Thank you for signing up! You will receive an invitation to access your account upon approval.');
}


// ## Project Classes
require_once(CLASSES_PATH.'/usage_handler.class.php');
require_once(CLASSES_PATH.'/execution_time.class.php');
require_once(CLASSES_PATH.'/ucrm_api.class.php');
require_once(CLASSES_PATH.'/ucrm_handler.class.php');
require_once(CLASSES_PATH.'/stripe_api.class.php');
require_once(CLASSES_PATH.'/stripe_handler.class.php');
require_once(CLASSES_PATH.'/payment_processor.class.php');

// ## include project scripts
require_once(PROJECT_SRC_PATH.'/config.php'); // Project configuration
require_once(PROJECT_SRC_PATH.'/functions.php'); // Project functions
