<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $options->ucrmPublicUrl; ?> - Signup</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="ucrm-client-signup-form/config/environment" content="%7B%22modulePrefix%22%3A%22ucrm-client-signup-form%22%2C%22environment%22%3A%22production%22%2C%22rootURL%22%3A%22/%22%2C%22locationType%22%3A%22none%22%2C%22EmberENV%22%3A%7B%22FEATURES%22%3A%7B%7D%2C%22EXTEND_PROTOTYPES%22%3A%7B%22Date%22%3Afalse%7D%7D%2C%22APP%22%3A%7B%22rootElement%22%3A%22%23ember-signup<?php echo $configMetadata ?>%22%2C%22name%22%3A%22ucrm-client-signup-form%22%2C%22version%22%3A%221.0.0+5acad376%22%7D%2C%22exportApplicationGlobal%22%3Afalse%7D" />

    <style type="text/css">
      <?php // ## UCRM requires file paths, Using PHP include instead of HTML tags to avoid relative URL ?>
      <?php include(PROJECT_PATH."/public/vendor-463d4d71894dfde19d720aa6b937502f.css"); ?>
      <?php include(PROJECT_PATH."/public/ucrm-client-signup-form-500a5c0e9df67704f365edc02f483591.css"); ?>
    </style>
    
  </head>
  <body>
    <?php if (!empty($config['LOGO_URL'])) { ?>
      <img src="<?php echo htmlspecialchars($config['LOGO_URL'], ENT_QUOTES); ?>" class="logo">
    <?php } ?>

    <?php if (!empty($config['FORM_TITLE'])) { ?>
      <h1 class="text-center mt-2"><?php echo htmlspecialchars($config['FORM_TITLE'], ENT_QUOTES); ?></h1>
    <?php } ?>

    <br clear="all">
    <?php if (!empty($config['FORM_DESCRIPTION'])) { ?>
      <div class="form-description">
        <?php echo htmlspecialchars($config['FORM_DESCRIPTION'], ENT_QUOTES); ?>
      </div>
      <br clear="all">
    <?php } ?>
    
    <div id="ember-signup"></div>
    <div id="ember-bootstrap-wormhole"></div>
    <div id="ember-basic-dropdown-wormhole"></div>
    
    <script type="text/javascript">
      <?php // ## UCRM requires file paths, Using PHP include instead of HTML tags to avoid relative URL ?>
      <?php include(PROJECT_PATH."/public/vendor-9bfe2b44f19210a7c1959ef10ea382e2.js"); ?>
      <?php include(PROJECT_PATH."/public/ucrm-client-signup-form-033a2b5d5cc7a1be36d89c96b7d6b608.js"); ?>
    </script>
  </body>
</html>