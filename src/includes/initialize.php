<?php
# SDK Data Variables
$configManager = \Ubnt\UcrmPluginSdk\Service\PluginConfigManager::create();
$config = $configManager->loadConfig();

$optionsManager = \Ubnt\UcrmPluginSdk\Service\UcrmOptionsManager::create();
$options = $optionsManager->loadOptions();

$ucrmSecurity = \Ubnt\UcrmPluginSdk\Service\UcrmSecurity::create();
$user = $ucrmSecurity->getUser();

## Just a unique key to give to ember for extra security when making requests
// $key = password_hash($options->pluginPublicUrl.PROJECT_PATH, PASSWORD_DEFAULT); // This does not work
$key = "this_key_should_be_improved";

\Ucsp\Interpreter::setFrontendKey($key);

$generateLead = $config["LEAD"] ? "yes" : "no";
$adminRoute = !empty($_GET['admin']) ? $_GET['admin'] : "false";

$envVariables = [
  'host' => $options->pluginPublicUrl,
  'completionText' => rawurlencode((string)$config["COMPLETION_TEXT"]),
  'frontendKey' => \Ucsp\Interpreter::getFrontendKey(),
  'isLead' => $generateLead,
  'pluginTranslation' => rawurlencode((string)$config["PLUGIN_TRANSLATION"]),
  'collectPayment' => rawurlencode((string)$config["COLLECT_PAYMENT"]),
  'initialRoute' => rawurlencode($adminRoute)
];

$prefix = '%22%2C%22';
$suffix = '%22%3A%22';
$configMetadata = '';

foreach ($envVariables as $key => $value) {
  $configMetadata .= $prefix . $key . $suffix . $value;
}

