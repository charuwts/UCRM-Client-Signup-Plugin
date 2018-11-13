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
    submit(client) {
      client.validate().then(({ validations }) => {
        if (validations.get('isValid')) {
          this.get('changeRoute')('signup.services');
        }
      });
    }

  }
});
