<?php require_once(PROJECT_PATH.'/includes/initialize.php'); ?>

<?php
  // file_put_contents(PROJECT_PATH.'/data/plugin.log', '', LOCK_EX);
  $service_json_file = PROJECT_PATH.'/data/services_filter.json'; 
  $data = [];

  if (file_exists($service_json_file)) {
    $jsonString = file_get_contents($service_json_file);
    $data = json_decode($jsonString, true);
  }

  if (empty($data)) {
    $data =  [
      "no_service_selected" => true
    ];
  }
  $isAdmin = UcrmHandler::isAdmin();
  if ($isAdmin) {
?>

  <!DOCTYPE html>
  <html>
    <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title>Services - UCRM Client Signup Plugin</title>
      <meta name="description" content="">
      <meta name="viewport" content="width=device-width, initial-scale=1">

      <meta name="ember-admin/config/environment" content="%7B%22modulePrefix%22%3A%22ember-admin%22%2C%22environment%22%3A%22production%22%2C%22rootURL%22%3A%22/%22%2C%22locationType%22%3A%22hash%22%2C%22EmberENV%22%3A%7B%22FEATURES%22%3A%7B%7D%2C%22EXTEND_PROTOTYPES%22%3A%7B%22Date%22%3Afalse%7D%7D%2C%22APP%22%3A%7B%22name%22%3A%22ember-admin%22%2C%22version%22%3A%220.0.0+24bc52c6%22%7D%2C%22exportApplicationGlobal%22%3Afalse%7D" />

      <style type="text/css">
        <?php // ## UCRM requires file paths, Using PHP include instead of HTML tags to avoid relative URL ?>
        <?php include(PROJECT_PATH."/admin-assets/vendor-d3aa84b783735f00b7be359e81298bf2.css"); ?>
        <?php include(PROJECT_PATH."/admin-assets/ember-admin-105f40e27128ab378b3355c0b3b355b8.css"); ?>
      </style>
      
    </head>
    <body>

      <div class="container-fluid">
        <div class="row py-4">
          <div class="col-auto">
            <pre>
              <?php print_r($data); ?>
            </pre>
            <p>
              Welcome Admin
            </p>
          </div>
        </div>
      </div>

      <div id="ember-signup"></div>
      <div id="ember-bootstrap-wormhole"></div>

      <script type="text/javascript">
        <?php // ## UCRM requires file paths, Using PHP include instead of HTML tags to avoid relative URL ?>
        <?php include(PROJECT_PATH."/admin-assets/vendor-d31c9db62cf94f369b6b7dc4b963ae45.js"); ?>
        <?php include(PROJECT_PATH."/admin-assets/ember-admin-402d1a45a4bab7eb2a0717d21a97ff4d.js"); ?>
      </script>

    </body>
  </html>



<?php
    $newJsonString = json_encode($data);
    file_put_contents($service_json_file, $newJsonString);
  } else {
    echo 'You do not have permission to access this page.';
  }
?>
