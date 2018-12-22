<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

chdir(__DIR__);
define("PROJECT_PATH", __DIR__);

include(PROJECT_PATH.'/includes/initialize.php');
include(PROJECT_PATH.'/includes/api-interpreter.php');


$ucrmSecurity = \Ubnt\UcrmPluginSdk\Service\UcrmSecurity::create();
$user = $ucrmSecurity->getUser();

$pluginLogManager = new PluginLogManager();
$pluginLogManager->appendLog(json_encode($user));

include(PROJECT_PATH.'/includes/ember-html.php');
