import Controller from '@ember/controller';
import { inject as service } from '@ember/service';
import ENV from "../../config/environment";

export default Controller.extend({
  ajax: service(),
  actions: {
    saveChanges() {
      this.set('error', false);
      this.get('ajax').post(ENV.APP.host, {
        data: {
          frontendKey: ENV.APP.frontendKey,
          api: {
            type: 'UPDATE_FILE',
            endpoint: 'plugin-config',
            data: this.get('model.pluginConfig')
          }
        } 
      }).catch((error) => {
        this.set('error', error.message);
      }).then((newConfig) => {
        if (this.get('error') === false) {
          this.set("notice", "Plugin Config Saved");
          this.set("model.pluginConfig", newConfig);
        }
      });
    }
  }
});
