<?php
# SDK Data Variables
$configManager = \Ubnt\UcrmPluginSdk\Service\PluginConfigManager::create();
$config = $configManager->loadConfig();

$optionsManager = \Ubnt\UcrmPluginSdk\Service\UcrmOptionsManager::create();
$options = $optionsManager->loadOptions();

$ucrmSecurity = \Ubnt\UcrmPluginSdk\Service\UcrmSecurity::create();
$user = $ucrmSecurity->getUser();

// # Data path for configuration writing
$dataUrl = PROJECT_PATH.'/data/';
\Ucsp\Interpreter::setDataUrl($dataUrl);

## Just a unique key to give to ember for extra security when making requests
$key = base64_encode(random_bytes(48));
\Ucsp\Interpreter::setFrontendKey($key);


$Generator = new \Ucsp\Generator();
$Generator->createCustomAttributes();

$generateLead = $config["LEAD"] ? "yes" : "no";
$adminRoute = !empty($_GET['admin']) ? $_GET['admin'] : "no";

$envVariables = [
  'host' => $options->pluginPublicUrl,
  'completionText' => rawurlencode((string)$config["COMPLETION_TEXT"]),
  'frontendKey' => \Ucsp\Interpreter::getFrontendKey(),
  'isLead' => $generateLead,
  'collectPayment' => rawurlencode((string)$config["COLLECT_PAYMENT"]),
  'initialRoute' => rawurlencode($adminRoute)
];

$prefix = '%22%2C%22';
$suffix = '%22%3A%22';
$configMetadata = '';

foreach ($envVariables as $key => $value) {
  $configMetadata .= $prefix . $key . $suffix . $value;
}

