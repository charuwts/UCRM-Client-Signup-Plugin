import Route from '@ember/routing/route';
import { inject as service } from '@ember/service';
import { hash } from 'rsvp';
import ENV from "../../config/environment";

export default Route.extend({
  ajax: service(),

  model() {
    return hash({
      translations: this.get('ajax').post(ENV.APP.host, {
        data: {
          frontendKey: ENV.APP.frontendKey,
          api: {
            type: 'VIEW_FILE',
            endpoint: 'translations'
          }
        } 
      }),
      currentTranslation: this.get('ajax').post(ENV.APP.host, {
        data: {
          frontendKey: ENV.APP.frontendKey,
          api: {
            type: 'VIEW_FILE',
            endpoint: 'current-translation'
          }
        } 
      }),
      selectedTranslation: this.get('ajax').post(ENV.APP.host, {
        data: {
          frontendKey: ENV.APP.frontendKey,
          api: {
            type: 'VIEW_FILE',
            endpoint: 'current-translation'
          }
        } 
      }),
    })
  }

});
