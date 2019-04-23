<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $options->ucrmPublicUrl; ?> - Signup</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="ucrm-client-signup-form/config/environment" content="%7B%22modulePrefix%22%3A%22ucrm-client-signup-form%22%2C%22environment%22%3A%22production%22%2C%22rootURL%22%3A%22/%22%2C%22locationType%22%3A%22none%22%2C%22EmberENV%22%3A%7B%22FEATURES%22%3A%7B%7D%2C%22EXTEND_PROTOTYPES%22%3A%7B%22Date%22%3Afalse%7D%7D%2C%22APP%22%3A%7B%22rootElement%22%3A%22%23ember-signup<?php echo $configMetadata ?>%22%2C%22name%22%3A%22ucrm-client-signup-form%22%2C%22version%22%3A%221.0.0+5acad376%22%7D%2C<?php echo $stripePublishableKeyEncoded ?>%22exportApplicationGlobal%22%3Afalse%7D" />

    <?php $publicUrl = str_replace(".php", "/", $options->pluginPublicUrl); ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $publicUrl.'vendor-463d4d71894dfde19d720aa6b937502f.css' ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo $publicUrl.'ucrm-client-signup-form-500a5c0e9df67704f365edc02f483591.css' ?>">

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
    <script type="text/javascript" src="https://js.stripe.com/v3/"></script>
    <div id="ember-signup"></div>
    <div id="ember-bootstrap-wormhole"></div>
    <div id="ember-basic-dropdown-wormhole"></div>
    
    <script type="text/javascript" src="<?php echo $publicUrl.'vendor-9bfe2b44f19210a7c1959ef10ea382e2.js" integrity="sha256-M+/KRWyBtyTWc9rn6PxhvzD2mlK+kPetkkj4DGujqis= sha512-M7o5+umKQAREno4oYg1Q32EJ8xRzVNLlj1CM9Nie4Uakkyk1D6LY1mQY4n7yIWlZMDuCmiec0Myd7RwqI5qvPg=='; ?>"></script>
    <script type="text/javascript" src="<?php echo $publicUrl.'ucrm-client-signup-form-349bb38dcd8aa6d61a37b5a305477271.js" integrity="sha256-QxUnassKO4Oo7pE9ts0DWpOzoogvM7gAHVZF35cgvc8= sha512-Amrhk8Ruz/9vjDaZSWg8G8zd0AUmVzna9+ZdieP+M2RdTRobxyZO0JxjkAvwAdpcEaGA5/g69wrCNmZ+WB0uKA=='; ?>"></script>
  </body>
</html>