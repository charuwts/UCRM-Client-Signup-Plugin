'use strict';

module.exports = function(environment) {
  let ENV = {
    modulePrefix: 'ucrm-client-signup-form',
    environment,
    rootURL: '/',
    locationType: 'hash',
    EmberENV: {
      FEATURES: {
        // Here you can enable experimetal features on an ember canary build
        // e.g. 'with-controller': true
      },
      EXTEND_PROTOTYPES: {
        // Prevent Ember Data from overriding Date.parse.
        Date: false
      }
    },

    APP: {
      // Here you can pass flags/options to your application instance
      // when it is created
      rootElement: '#ember-signup',
      // host: 'http://192.168.1.5:8080/_plugins/ucrm-client-signup-plugin/public.php',
      host: 'http://192.168.1.5:8080/_plugins/ucrm-client-signup-plugin/public.php',
      frontendKey: 'xHLYmrk8yR5sMfW9uDbaHN74tVbTH2mwan+Ix0xFvWrM7EpuYMY+h8njX+g9CCpE',
      useCountrySelect: 'false',
      isLead: 'no',
      collectPayment: 'no'
    }
  };

  if (environment === 'development') {
    // ENV.APP.LOG_RESOLVER = true;
    // ENV.APP.LOG_ACTIVE_GENERATION = true;
    // ENV.APP.LOG_TRANSITIONS = true;
    // ENV.APP.LOG_TRANSITIONS_INTERNAL = true;
    // ENV.APP.LOG_VIEW_LOOKUPS = true;
    ENV.stripe = {
      publishableKey: 'pk_test_qsnFvCBr4Zc41K7EpEYWvYTh'
    };

  }

  if (environment === 'test') {
    ENV.APP.host = 'http://localhost:8080/_plugins/ucrm-client-signup-plugin/public.php';
    ENV.APP.completionText = 'completiontextinformation';
    ENV.APP.frontendKey = 'this_key_should_be_improved';
    // Testem prefers this...
    ENV.locationType = 'none';

    // keep test console output quieter
    ENV.APP.LOG_ACTIVE_GENERATION = false;
    ENV.APP.LOG_VIEW_LOOKUPS = false;

    ENV.APP.rootElement = '#ember-testing';
    ENV.APP.autoboot = false;

    ENV.stripe = {
      publishableKey: 'pk_test_qsnFvCBr4Zc41K7EpEYWvYTh'
    };
  }

  if (environment === 'production') {
    // here you can enable a production-specific feature
    ENV.stripe = {
      publishableKey: 'pk_test_qsnFvCBr4Zc41K7EpEYWvYTh'
    };

  }

  return ENV;
};
