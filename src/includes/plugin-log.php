<?php

if (!empty($_GET['log'])) {
    if (! $user || ! $user->hasViewPermission(\Ubnt\UcrmPluginSdk\Security\PermissionNames::SYSTEM_PLUGINS)) {
      if (! headers_sent()) {
          header("HTTP/1.1 403 Forbidden");
      }
      die('You do not have permission to see this page.');
    }
    if ($_GET['log'] == 'clear') {
      file_put_contents(\Ucsp\Interpreter::$dataUrl.'log.log', '');
      header("LOCATION: public.php?log=plugin-log");
    }

    $stylesheet_vendor = '<link rel="stylesheet" type="text/css" href="'.str_replace(".php", "/", $options->pluginPublicUrl).'vendor-463d4d71894dfde19d720aa6b937502f.css">';
    $stylesheet = '<link rel="stylesheet" type="text/css" href="'.str_replace(".php", "/", $options->pluginPublicUrl).'ucrm-client-signup-form-500a5c0e9df67704f365edc02f483591.css">';
    echo $stylesheet_vendor;
    echo $stylesheet;
    echo '<div class="wrapper">';
    echo '<a href="https://www.charuwts.com/plugins/ucrm-signup" target="_blank"><img src="https://s3.amazonaws.com/charuwts.com/images/charuwts-logo.png" class="fit-image logo-image"></a>';
    echo '<h3 class="mt-3">UCRM Client Signup Form Logs</h3>';
    echo '<p>This log is for specific errors and processes and will also be improved in the future to have filters for types of log information. Some logs are also reported on the plugin details page.</p>';
    echo '<a href="public.php?log=clear"><button class="btn btn-danger my-3">Clear Logs</button></a>';
    echo '<pre><div class="code-wrapper">';
    $logfile = \Ucsp\Interpreter::$dataUrl.'log.log';
    if (file_exists($logfile)) {
      echo file_get_contents($logfile);
    }
    echo '</div></pre>';

    echo '<style type="text/css">
      .code-wrapper {
        background-color: #EEE;
        max-width: 100%;
        width: 1000px;
        overflow: auto;
        padding: 20px;
      }
      .logo-image {
        width: 400px;
      }
      h3 {
        font-size: 1.2rem;
      }
      .fit-image {
        display: block;
        max-width: 100%;
      }
      .wrapper {
        background-color: white;
        padding: 20px;
      }
    </style>';

    exit();
}

?>