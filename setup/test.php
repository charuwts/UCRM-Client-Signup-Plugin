<?php
chdir(__DIR__);
define("PROJECT_PATH", __DIR__);

require_once(PROJECT_PATH.'/../src/vendor/autoload.php');

echo 'this is a test';
// require_once(PROJECT_PATH.'/../src/includes/classes/usage_handler.class.php');
// require_once(PROJECT_PATH.'/../src/includes/classes/ucrm_api.class.php');
// require_once(PROJECT_PATH.'/../src/includes/classes/ucrm_handler.class.php');

// UcrmApi::setUcrmKey('z8q2jBvzlWMVx73jYnlTJtidBoM2Q/YZDubIni5GaYX607FCYIthrTEKd8ePs7kP');
// UcrmApi::setUcrmApiUrl('http://ucrm.dev.ellerslie.com/api/v2.9');

// $current_period_start = date('c', 1535146395);

// echo $current_period_start;
// $installation_note = "Lightgig internet installation fee, scheduled to take place: 11/11/11 \r\n\r\n No one likes being in the dark when it comes to why the internet is out. Although we never anticipate an internet outages, if it happens, as a LightGig Customer you will get email alerts when there is a widespread internet outage and we will also let you know when the internet is back on. If you ever want to unsubscribe from these courtesy email alerts, please let us know and we will be happy to remove you from the list. We hope you enjoy LighgGig!";

// $ucrm_handler = new UcrmHandler;

// $payment = $ucrm_handler->createPayment(null, 190, 29, 66, $installation_note);

// // ## Notify Client
// $ucrm_handler->sendReceipt($payment->id);
// print_r($payment);


