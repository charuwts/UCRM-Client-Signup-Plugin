
<?php
if (!empty($_GET['admin'])) {
    if ($_GET['admin'] == 'service-filters') {
      if (! $user || ! $user->hasViewPermission(\Ubnt\UcrmPluginSdk\Security\PermissionNames::SYSTEM_PLUGINS)) {
        if (! headers_sent()) {
            header("HTTP/1.1 403 Forbidden");
        }
        die('You do not have permission to see this page.');
      }
?>
  <!DOCTYPE html>
  <html>
    <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title>Services - UCRM Client Signup Plugin</title>
      <meta name="description" content="">
      <meta name="viewport" content="width=device-width, initial-scale=1">

      <meta name="ember-admin/config/environment" content="%7B%22modulePrefix%22%3A%22ember-admin%22%2C%22environment%22%3A%22production%22%2C%22rootURL%22%3A%22/%22%2C%22locationType%22%3A%22hash%22%2C%22EmberENV%22%3A%7B%22FEATURES%22%3A%7B%7D%2C%22EXTEND_PROTOTYPES%22%3A%7B%22Date%22%3Afalse%7D%7D%2C%22APP%22%3A%7B%22rootElement%22%3A%22%23ember-admin%22%2C%22frontendKey%22%3A%22<?php echo \Ucsp\Interpreter::getFrontendKey(); ?>%22%2C%22host%22%3A%22<?php echo $options->pluginPublicUrl; ?>%22%2C%22name%22%3A%22ember-admin%22%2C%22version%22%3A%220.0.0+b68d0ec3%22%7D%2C%22exportApplicationGlobal%22%3Afalse%7D" />

      <style type="text/css">
        <?php // ## UCRM requires file paths, Using PHP include instead of HTML tags to avoid relative URL ?>
        <?php include(PROJECT_PATH."/admin-assets/vendor-d3aa84b783735f00b7be359e81298bf2.css"); ?>
        <?php include(PROJECT_PATH."/admin-assets/ember-admin-36205835274af8213a309ecdbb2ed3c6.css"); ?>
      </style>
      
    </head>
    <body>
      <div id="ember-admin"></div>
      <div id="ember-bootstrap-wormhole"></div>

      <script type="text/javascript">
        <?php // ## UCRM requires file paths, Using PHP include instead of HTML tags to avoid relative URL ?>
        <?php include(PROJECT_PATH."/admin-assets/vendor-b243ab3710c152e7c3a92648bca62b18.js"); ?>
        <?php include(PROJECT_PATH."/admin-assets/ember-admin-59bce98a9cd85b88a6973e1ad17ae791.js"); ?>
      </script>

    </body>
  </html>
<?php
  exit();
  } else {
    echo 'You do not have permission to access this page.';
  }
}
?>
