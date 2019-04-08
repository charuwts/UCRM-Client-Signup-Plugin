<?php

require_once __DIR__ . '/vendor/autoload.php';

chdir(__DIR__);
define("PROJECT_PATH", __DIR__);

$stripe = new \Ucsp\Stripe();
$stripe->processPayments();