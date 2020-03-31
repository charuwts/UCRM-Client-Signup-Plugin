import Component from '@ember/component';
import { computed } from '@ember/object';

export default Component.extend({
  translation: computed('key', function() {
    return this.get('translate.'+this.get('key'));
  })
});
