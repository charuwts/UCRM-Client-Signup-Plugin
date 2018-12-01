import Component from '@ember/component';
import { inject as service } from '@ember/service';
import { computed } from '@ember/object';
import ENV from "../config/environment";

export default Component.extend({
  ajax: service(),
  store: service(),
  classNames: ['container-fluid'],
  states: computed('model.client.countryId', function() {
    if ((this.get('model.client.countryId') == 249) || (this.get('model.client.countryId') == 54)) {
      return this.get('ajax').post(ENV.APP.host, {
        data: {
          pluginAppKey: ENV.APP.pluginAppKey,
          country_id: this.get('model.client.countryId')
        }
      });
    } else {
      this.set('model.client.stateId', null);
      return false;
    }
  }),

  actions: {
    selectCountry(country) {
      this.set('model.client.countryId', country.id);
      this.set('selectedCountry', country);

      this.set('model.client.stateId', null);
      this.set('selectedState', null);
    },
    selectState(state) {
      this.set('model.client.stateId', state.id);
      this.set('selectedState', state);
    },
    submit(client) {
      client.validate().then(({ validations }) => {
        if (validations.get('isValid')) {
          this.get('changeRoute')('signup.services');
        }
      });
    }

  }
});
