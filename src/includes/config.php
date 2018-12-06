<?php
/* 
 * Copyright © 2018 · Charuwts, LLC
 * All rights reserved.
 * You may not redistribute or modify the Software of Charuwts, LLC, and you are prohibited from misrepresenting the origin of the Software.
 * 
 */


// Composer Files
require_once(PROJECT_PATH.'/vendor/autoload.php');

// Error Reporting
error_reporting(E_ALL); // Reports all errors
ini_set('display_errors','Off'); // Do not display errors for the end-users (security issue)
ini_set('error_log', PROJECT_PATH.'/data/plugin.log'); // Set a logging file

// Override the default error handler behavior
set_exception_handler(function($exception) {
 error_log($exception);
});

// Stripe
\Stripe\Stripe::setApiKey(\UCSP\Config::$STRIPE_SECRET_KEY);

// StripeApi::setEndpointSecret(\UCSP\Config::$STRIPE_WEBHOOK_SECRET);

\UCSP\UcrmApi::setUcrmKey(UCRM_KEY);
\UCSP\UcrmApi::setUcrmApiUrl(UCRM_API_URL);
