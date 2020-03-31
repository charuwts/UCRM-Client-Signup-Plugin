import Controller from '@ember/controller';
import { computed } from '@ember/object';
import { inject as service } from '@ember/service';
import ENV from "../../config/environment";

export default Controller.extend({
  ajax: service(),

  selectedLanguage: null,
  translationOptions: computed('model.translations', function() {
    let keys = [];
    for (var key in this.model.translations) {
      keys.push(key);
    }
    return keys;
  }),

  actions: {
    selectLanguage(selectedLanguage) {
      this.set('selectedLanguage', selectedLanguage);
      return this.set("model.selectedTranslation", this.get('model.translations.'+selectedLanguage));
    },
    saveChanges() {
      this.set('error', false);
      this.get('ajax').post(ENV.APP.host, {
        data: {
          frontendKey: ENV.APP.frontendKey,
          api: {
            type: 'UPDATE_FILE',
            endpoint: 'current-translation',
            data: this.get('model.selectedTranslation')
          }
        } 
      }).catch((error) => {
        this.set('error', error.message);
      }).then((newTranslation) => {
        if (this.get('error') === false) {
          this.set("notice", "Translation Generated");
          this.set("model.selectedTranslation", newTranslation);
        }
      });
    },
  }
});
