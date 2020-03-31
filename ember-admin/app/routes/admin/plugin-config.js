import Route from '@ember/routing/route';
import { hash } from 'rsvp';
import { inject as service } from '@ember/service';
import ENV from "../../config/environment";

export default Route.extend({
  ajax: service(),
  model() {
    return hash({
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
  }
});
