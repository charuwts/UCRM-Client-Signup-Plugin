<?php
# SDK Data Variables
$configManager = \Ubnt\UcrmPluginSdk\Service\PluginConfigManager::create();
$config = $configManager->loadConfig();

$optionsManager = \Ubnt\UcrmPluginSdk\Service\UcrmOptionsManager::create();
$options = $optionsManager->loadOptions();

$ucrmSecurity = \Ubnt\UcrmPluginSdk\Service\UcrmSecurity::create();
$user = $ucrmSecurity->getUser();

$PluginConfig = new \Ucsp\Config();
$plugin_config = $PluginConfig->viewFile('plugin-config');


// # Data path for configuration writing
$dataUrl = PROJECT_PATH.'/data/';
\Ucsp\Interpreter::setDataUrl($dataUrl);

## Just a unique key to give to ember for extra security when making requests
// $key = base64_encode(random_bytes(48));
// \Ucsp\Interpreter::setFrontendKey($key);
\Ucsp\Interpreter::setFrontendKey('this_key_should_be_improved');

$generateLead = $config["LEAD"] ? "yes" : "no";
$stripeKey = !empty($plugin_config["PUBLISHABLE_KEY"]) ? $plugin_config["PUBLISHABLE_KEY"] : "no";
$adminRoute = !empty($_GET['admin']) ? $_GET['admin'] : "no";

$envVariables = [
  "host" => $options->pluginPublicUrl,
  "frontendKey" => \Ucsp\Interpreter::getFrontendKey(),
  "isLead" => $generateLead,
  "collectPayment" => rawurlencode((string)$config["COLLECT_PAYMENT"]),
  "initialRoute" => rawurlencode($adminRoute)
];
$stripePublishableKeyEncoded = "%22stripe%22%3A%7B%22publishableKey%22%3A%22".$stripeKey."%22%7D%2C";
// Encode to json string, remove first and last characters { } and rawurlencode the string
// $configMetadata = rawurlencode(substr(substr(json_encode($envVariables), 1), 0, -3));

$prefix = '%22%2C%22';
$suffix = '%22%3A%22';
$configMetadata = '';

foreach ($envVariables as $key => $value) {
  if ($key == 'stripe') {
    $configMetadata .= $prefix . $key . $value;
  } else {
    $configMetadata .= $prefix . $key . $suffix . $value;
  }
}

// %2c: ,
// %22: "
// %3A: :
// %7B: {
// %7D: }