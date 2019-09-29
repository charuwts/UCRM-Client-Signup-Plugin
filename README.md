This repository is no longer maintained and was not compatible with UNMS when archived. You can fork the project and continue development if you're interested in maintaining it.

# UCRM Client Signup
A [UCRM plugin](https://github.com/Ubiquiti-App/UCRM-plugins) that allows a customer to signup via a checkout format.

_Developed by [Charuwts, LLC](https://charuwts.com)_

When installed onto UCRM the plugin public URL will display a form that anyone can enter valid info into to create a client. The aim of the plugin is to simplify the process of transitioning a prospective customer to a client so development is ongoing to seek to improve upon this aim.

[Documentation for the UCRM Client Signup](https://github.com/charuwts/UCRM-Client-Signup/wiki)



## Summary

### src/main.php
Runs $stripe->processPayments(); each time UCRM "executes" the script. Defined in src/ucsp/Stripe.php:150

### src/public.php 

Handles the admin pages and frontend form. Each src/includes file is run in order so that the proper data is rendered based on the request.

#### src/includes/api-interpreter.php 

Defines the api endpoints that the frontend form is allowed to access. Since a UCRM api key grants access to everything, this api middleware allows for limiting exposing that to the world via the plugin. This file runs before any of the other included files and checks for a payload with a frontendKey and valid data. See src/ucsp/Interpreter.php for the class definition.

#### src/includes/plugin-log.php
Displays a unique log page that was intended to have more features then just logging to the plugin settings page, but for now operates somewhat similar with an option to clear the logs.

#### src/includes/embed-code.php 
Generates an ember embed code. This is very similar to what is used in ember-html.php. ember.js once compiled needs 4 files and a metatag to run. The 4 files can be found under src/public for the frontend and src/admin-assets for the backend.

The content in the metatag name="ucrm-client-signup-form/config/environment" defines environment variables that the app will use. And these are contained in the $configMetadata variable that is defined in initialze.php:25-48. As well as $stripePublishableKeyEncoded, which is either a key or the text "no" assigned on line 22. Some kind of string needs to be applied to the stripe key or ember-stripe-elements will throw an error because it's expected to be assigned in the environment before the app starts. Currently the app will break if "collect payment" is true and the stripe key is invalid.

The default environment variables are setup for ember in ember-src/config/environment.js when compiling the app. But these are what is overritten in src/includes/ember-embed.php in the metatag. Basically whatever is set in environment.js when building for production is set to the environment meta tag converted to a url safe json object.

#### src/includes/admin.php 
Sets up the admin ember application and is restricted to users with plugin permissions. Setup similar to src/includes/ember-html.php

#### src/includes/ember-html.php 
Will be the catch all for anything that hits __plugin-url__/public.php on ucrm and display the form if no special data is found in the previous includes.

#### Some other things worth noting

* in src/
  * run `composer update` to install packages
  * run `vendor/bin/phpunit` to run tests "__warning: some tests may need access to the database, do not run on a live ucrm instance__"
  * run `vendor/bin/pack-plugin` to compress project for ucrm __this will remove composer packages, so you'll need to run composer update again to use phpunit__

* There are some variables in the environment that could be moved to the admin config since they don't need to be available on initialization.
* To update the form, make changes in ember-src and run `ember build -prod`, remove the old 4 assets from src/public then copy the 4 files from the ember-src/dist/assets into src/public. Then update the filename and integrity sha in src/includes/ember-html.php and src/includes/embed-code.php
