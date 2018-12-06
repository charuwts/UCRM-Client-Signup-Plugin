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
          pluginAppKey: ENV.APP.pluginAppKey,
          servicePlanFilters: true
        } 
      }),
      countries: this.get('ajax').post(ENV.APP.host, {
        data: {
          pluginAppKey: ENV.APP.pluginAppKey,
          countries: true
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
