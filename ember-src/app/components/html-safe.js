import Component from '@ember/component';
import { htmlSafe } from '@ember/template';
import { computed } from '@ember/object';

export default Component.extend({
  tagName: '',
  htmlSafe: computed('content', function() {
    return htmlSafe(this.get('content'));
  })
});
