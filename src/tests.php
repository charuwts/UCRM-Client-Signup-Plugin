<?php
chdir(__DIR__);
define("PROJECT_PATH", __DIR__);

require_once(PROJECT_PATH.'/../src/includes/initialize.php'); // Project functions
// UsageHandler::increment_signup(46, 16);
$response = UsageHandler::validate();
print_r($response);
// $arr = [2,3,4];
// UsageHandler::increment_invoices($arr, count($arr));

// # Lightgig test
// \Stripe\Stripe::setApiKey("sk_test_mrhveUUktyn3akYEKWlC1GtU");

// $subscription = \Stripe\Subscription::retrieve("sub_DHhit8T9xE3SSr");


// \Stripe\SubscriptionItem::create(array(
//   "subscription" => $subscription->id,
//   "plan" => "ucrm-client-signup-plugin-invoices",
// ));
// \Stripe\UsageRecord::create(array(
//   "quantity" => 1,
//   "timestamp" => time(),
//   "subscription_item" => 'si_DHhiK4q2fmsCWX'
// ));

// echo '<pre>';
// print_r($subscription);
// echo '</pre>';

