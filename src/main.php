<?php
chdir(__DIR__);

define("PROJECT_PATH", __DIR__);

require_once(PROJECT_PATH.'/includes/initialize.php');

$mark = new ExecutionTime();
$mark->start(); 

$sync_stripe = new SyncStripe;
$sync_stripe->convertProductsToServicePlans();
$sync_stripe->syncSubscriptionsToServices();

$mark->end();
log_event('Time to Sync', (string)$mark->diff(). ' ms'); 