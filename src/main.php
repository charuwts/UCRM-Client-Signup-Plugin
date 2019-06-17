<?php

require_once __DIR__ . '/vendor/autoload.php';

chdir(__DIR__);
define("PROJECT_PATH", __DIR__);

include(PROJECT_PATH.'/includes/initialize.php');
$stripe = new \Ucsp\Stripe();
$stripe->processPayments();