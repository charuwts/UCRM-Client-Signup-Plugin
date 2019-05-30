<?php

if (!empty($_GET['embed'])) {
    // if (! $user || ! $user->hasViewPermission(\Ubnt\UcrmPluginSdk\Security\PermissionNames::SYSTEM_PLUGINS)) {
    //   if (! headers_sent()) {
    //       header("HTTP/1.1 403 Forbidden");
    //   }
    //   die('You do not have permission to see this page.');
    // }

    $metatag = '<meta name="ucrm-client-signup-form/config/environment" content="%7B%22modulePrefix%22%3A%22ucrm-client-signup-form%22%2C%22environment%22%3A%22production%22%2C%22rootURL%22%3A%22/%22%2C%22locationType%22%3A%22none%22%2C%22EmberENV%22%3A%7B%22FEATURES%22%3A%7B%7D%2C%22EXTEND_PROTOTYPES%22%3A%7B%22Date%22%3Afalse%7D%7D%2C%22APP%22%3A%7B%22rootElement%22%3A%22%23ember-signup'.$configMetadata.'%22%2C%22name%22%3A%22ucrm-client-signup-form%22%2C%22version%22%3A%221.0.0+5acad376%22%7D%2C'.$stripePublishableKeyEncoded.'%22exportApplicationGlobal%22%3Afalse%7D" />';


    $javascript_vendor = '<script type="text/javascript" src="'.str_replace(".php", "/", $options->pluginPublicUrl).'vendor-9bfe2b44f19210a7c1959ef10ea382e2.js"></script>';
    $javascript = '<script type="text/javascript" src="'.str_replace(".php", "/", $options->pluginPublicUrl).'ucrm-client-signup-form-349bb38dcd8aa6d61a37b5a305477271.js"></script>';


    $stylesheet_vendor = '<link rel="stylesheet" type="text/css" href="'.str_replace(".php", "/", $options->pluginPublicUrl).'vendor-463d4d71894dfde19d720aa6b937502f.css">';
    $stylesheet = '<link rel="stylesheet" type="text/css" href="'.str_replace(".php", "/", $options->pluginPublicUrl).'ucrm-client-signup-form-500a5c0e9df67704f365edc02f483591.css">';
    echo $stylesheet_vendor;
    echo $stylesheet;
    echo '<div class="wrapper">';
    echo '<a href="https://www.charuwts.com/plugins/ucrm-signup" target="_blank"><img src="https://s3.amazonaws.com/charuwts.com/images/charuwts-logo.png" class="fit-image logo-image"></a>';
    echo '<h3 class="mt-3">UCRM Client Signup Form Embed</h3>';
    echo '<p>Be sure to add the domain of where you display the form to the field "Allowed Public Origin" within the plugin config.</p>';
    echo '<h3>Insert in HEAD tag</h3><pre><div class="code-wrapper">';
    echo htmlspecialchars($metatag) . '<br>';
    echo htmlspecialchars($stylesheet_vendor) . '<br>';
    echo htmlspecialchars($stylesheet);
    echo '</div></pre>';
    echo '<h3>Insert anywhere within html to embed signup form.</h3><pre><div class="code-wrapper">';
    echo htmlspecialchars('<div id="ember-signup"></div>') . '<br>';
    echo htmlspecialchars('<div id="ember-bootstrap-wormhole"></div>') . '<br>';
    echo htmlspecialchars('<div id="ember-basic-dropdown-wormhole"></div>');
    echo '</div></pre>';
    echo '<h3>Insert after footer</h3><pre><div class="code-wrapper">';
    echo htmlspecialchars($javascript_vendor) . '<br>';
    echo htmlspecialchars($javascript);
    echo '</div></pre>';
    echo '</div>';
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
      .fit-image {
        display: block;
        max-width: 100%;
      }
      h3 {
        font-size: 1.2rem;
      }
      .wrapper {
        background-color: white;
        padding: 20px;
      }
    </style>';
    exit();
}

?>