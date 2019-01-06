import Service from '@ember/service';
import { inject as service } from '@ember/service';
import ENV from "../config/environment";

export default Service.extend({
  ajax: service(),

  fetchTranslation() {
    this.get('ajax').post(ENV.APP.host, {
      data: {
        frontendKey: ENV.APP.frontendKey,
        api: {
          type: 'VIEW_FILE',
          endpoint: 'current-translation'
        }
      } 
    }).then((translation) => {
      this.set('translation', translation);
    });
  },


  key(key) {
    return this.get('translation')[key];
  }
});
