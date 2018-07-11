<?php
chdir(__DIR__);

define("PROJECT_PATH", __DIR__);

require_once(PROJECT_PATH.'/includes/initialize.php');

file_put_contents(PROJECT_PATH.'/data/plugin.log', '', LOCK_EX);

$mark = new ExecutionTime();
$mark->start(); 

if (USE_STRIPE === true) {
  $payment_processor = new PaymentProcessor('STRIPE');
}

$payment_processor->processPayments();

$mark->end();

log_event('Executed Successfully', (string)$mark->diff(). ' ms'); 