import Component from '@ember/component';
import { inject as service } from '@ember/service';
import { computed } from '@ember/object';
import ENV from "../config/environment";

export default Component.extend({
  ajax: service(),
  store: service(),
  showTerms: false,
  agreedToTerms: false,

  classNames: ['container-fluid'],
  states: computed('model.client.countryId', function() {
    if ((this.get('model.client.countryId') == 249) || (this.get('model.client.countryId') == 54)) {
      return this.get('ajax').post(ENV.APP.host, {
        data: {
          frontendKey: ENV.APP.frontendKey,
          api: {
            type: 'GET',
            endpoint: 'countries/'+this.get('model.client.countryId')+'/states',
            data: {}
          }
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
    agreeToTerms() {
      this.toggleProperty('agreedToTerms');
    },
    viewTerms() {
      this.toggleProperty('showTerms');
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
