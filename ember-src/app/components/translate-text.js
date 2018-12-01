import Component from '@ember/component';
import { computed } from '@ember/object';
import { inject as service } from '@ember/service';

export default Component.extend({
  tagName: '',
  translateText: service(),

  translation: computed('key', function() {
    return this.get('translateText').key(this.get('key'));
  })
});
