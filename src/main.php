<?php
chdir(__DIR__);

define("PROJECT_PATH", __DIR__);

require_once(PROJECT_PATH.'/includes/initialize.php');

$mark = new ExecutionTime();
$mark->start(); 

if (USE_STRIPE === true) {
  $payment_processor = new PaymentProcessor('STRIPE');
}

$payment_processor->processPayments();



$mark->end();
log_event('Time to Sync', (string)$mark->diff(). ' ms'); 