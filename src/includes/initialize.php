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

$key = base64_encode(random_bytes(48));

$dataUrl = PROJECT_PATH.'/data/';
\Ucsp\Interpreter::setDataUrl($dataUrl);
\Ucsp\Interpreter::setFrontendKey($key);

$generateLead = $config["LEAD"] ? "yes" : "no";
$stripeKey = !empty($config["STRIPE_PUBLIC_KEY"]) ? $config["STRIPE_PUBLIC_KEY"] : "no";
$adminRoute = !empty($_GET['admin']) ? $_GET['admin'] : "no";

$envVariables = [
  "host" => $options->pluginPublicUrl,
  "frontendKey" => \Ucsp\Interpreter::getFrontendKey(),
  "isLead" => $generateLead,
  "collectPayment" => $config["COLLECT_PAYMENT"] == true ? 'yes' : 'no',
  "initialRoute" => rawurlencode($adminRoute)
];
if ($config["COLLECT_PAYMENT"] == true) {
  \Stripe\Stripe::setApiKey($config['STRIPE_SECRET_KEY']);
}

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