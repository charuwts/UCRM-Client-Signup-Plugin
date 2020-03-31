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
            endpoint: 'service-filters',
            data: this.get('model.servicePlanFilters')
          }
        } 
      }).catch((error) => {
        this.set('error', error.message);
      }).then((newServices) => {
        if (this.get('error') === false) {
          this.set("notice", "Service Filters Saved");
          this.set("model.servicePlanFilters", newServices);
        }
      });
    },
    regenerateServices() {
      this.set('error', false);
      this.get('ajax').post(ENV.APP.host, {
        data: {
          frontendKey: ENV.APP.frontendKey,
          api: {
            type: 'GET',
            endpoint: 'service-plans'
          }
        } 
      }).catch((error) => {
        this.set('error', error.message);
      }).then((fetchedServices) => {

        this.get('ajax').post(ENV.APP.host, {
          data: {
            frontendKey: ENV.APP.frontendKey,
            api: {
              type: 'UPDATE_FILE',
              endpoint: 'service-filters',
              data: fetchedServices
            }
          } 
        }).catch((error) => {
          this.set('error', error.message);
        }).then((newServices) => {
          if (this.get('error') === false) {
            this.set("notice", "Services Regenerated");
            this.set("model.servicePlanFilters", newServices);
          }
        });

      });
    }
  }
});
