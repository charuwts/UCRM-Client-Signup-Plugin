import Component from '@ember/component';
import { computed } from '@ember/object';

export default Component.extend({
  tagName: '',
  init() {
    if (!this.get('placeholder')) {
      let value = this.get('options').split(",")[0];
      this.set('selectedOption', value);
    }
    this._super(...arguments);
  },
  isDropdown: computed('options', function() {
    if (this.get('options')) {
      return true;
    } else {
      return false;
    }
  }),
  parsedOptions: computed('options', function() {
    if (this.get('options')) {
      return this.get('options').split(","); 
    }
  }),
  actions: {
    selectOption(opt) {
      this.set('value', opt);
      this.set('selectedOption', opt);
    }
  }


});
