<?php
/* 
 * Copyright © 2018 · Charuwts, LLC
 * All rights reserved.
 * You may not redistribute or modify the Software of Charuwts, LLC, and you are prohibited from misrepresenting the origin of the Software.
 * 
 */

// ## Project paths
define("PROJECT_SRC_PATH", PROJECT_PATH . '/includes');
define("CLASSES_PATH", PROJECT_PATH . '/includes/classes');

// ## Setup Environment Constants
$ucrm_string = file_get_contents(PROJECT_PATH."/ucrm.json");
$ucrm_json = json_decode($ucrm_string);
define("UCRM_PUBLIC_URL", $ucrm_json->ucrmPublicUrl);
define("UCRM_KEY", $ucrm_json->pluginAppKey);
define("PLUGIN_PUBLIC_URL", $ucrm_json->pluginPublicUrl);

// ## include project scripts
require_once(PROJECT_SRC_PATH.'/functions.php'); // Project functions

require_once(CLASSES_PATH.'/config.class.php');
$config_path = PROJECT_PATH."/data/config.json";
\UCSP\Config::initializeStaticProperties($config_path);
define("UCRM_API_URL", \UCSP\Config::PLUGIN_URL().'/api/v2.9');


// ## Just a unique key to give to ember for extra security when making requests
// $key = password_hash(PLUGIN_PUBLIC_URL, 'PASSWORD_DEFAULT');
// define("FRONTEND_PUBLIC_KEY", $key);
define("FRONTEND_PUBLIC_KEY", 'development_key');

// ## Setup user configuration settings, if they exist
if (file_exists($config_path)) {

  $config_string = file_get_contents($config_path);
  $config_json = json_decode($config_string);


  // ## Check if Stripe configuration is set, if so USE_STRIPE=true
  if (!empty(\UCSP\Config::$STRIPE_SECRET_KEY) && !empty(\UCSP\Config::$STRIPE_PUBLIC_KEY)) {
    define("USE_STRIPE", true);
  } else {
    define("USE_STRIPE", false);
  }
  
}

define("API_URL", 'https://api.charuwts.com/api/v1/subscriptions');


// ## Project Classes
require_once(CLASSES_PATH.'/usage_handler.class.php');
require_once(CLASSES_PATH.'/execution_time.class.php');
require_once(CLASSES_PATH.'/ucrm_api.class.php');
require_once(CLASSES_PATH.'/ucrm_handler.class.php');
require_once(CLASSES_PATH.'/stripe_api.class.php');
require_once(CLASSES_PATH.'/stripe_handler.class.php');
require_once(CLASSES_PATH.'/payment_processor.class.php');

require_once(PROJECT_SRC_PATH.'/config.php'); // Project configuration, must come after Config initialization and classes
