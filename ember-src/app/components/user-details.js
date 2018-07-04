import Component from '@ember/component';
import { inject as service } from '@ember/service';
import { computed } from '@ember/object';
import ENV from "../config/environment";

export default Component.extend({
  ajax: service(),
  store: service(),
  classNames: ['container-fluid'],
  useCountries: ENV.APP.useCountrySelect,

  states: computed('model.client.country.id', function() {
    if ((this.get('model.client.country.id') == 249) || (this.get('model.client.country.id') == 54)) {
      return this.get('ajax').post(ENV.APP.host, {
        data: {
          pluginAppKey: ENV.APP.pluginAppKey,
          country_id: this.get('model.client.country.id')
        } 
      });
      
    } else {
      this.set('model.client.state', null);
      return false;
    }

  }), 


  actions: {
    submit(client) {
      client.validate().then(({ validations }) => {
        if (validations.get('isValid')) {
          this.get('changeRoute')('signup.services');
        }
      });
    }

  }
});
