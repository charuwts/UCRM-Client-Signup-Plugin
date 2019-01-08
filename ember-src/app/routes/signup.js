import Route from '@ember/routing/route';
import { hash } from 'rsvp';
import { inject as service } from '@ember/service';
import ENV from "../config/environment";

export default Route.extend({
  ajax: service(),
  model() {
    return hash({
      servicePlanId: null,
      servicePlanPeriodId: null,
      client: this.get('store').createRecord('client'),
      servicePlans: this.get('ajax').post(ENV.APP.host, {
        data: {
          frontendKey: ENV.APP.frontendKey,
          api: {
            type: 'VIEW_FILE',
            endpoint: 'service-filters'
          }
        } 
      }),
      countries: this.get('ajax').post(ENV.APP.host, {
        data: {
          frontendKey: ENV.APP.frontendKey,
          api: {
            type: 'GET',
            endpoint: 'countries'
          }
        } 
      }),
      pluginConfig: this.get('ajax').post(ENV.APP.host, {
        data: {
          frontendKey: ENV.APP.frontendKey,
          api: {
            type: 'VIEW_FILE',
            endpoint: 'plugin-config'
          }
        } 
      }),
    })
  },
  // afterModel(afterModel) {
  //   if (ENV.APP.useCountrySelect !== 'TRUE') {
  //     afterModel.countries = null;
  //     afterModel.client.set('country', null);
  //     afterModel.client.set('state', null);
  //   }
  // }

});
