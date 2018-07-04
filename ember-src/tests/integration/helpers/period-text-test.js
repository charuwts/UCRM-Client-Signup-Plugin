import { moduleForComponent, test } from 'ember-qunit';
import hbs from 'htmlbars-inline-precompile';

moduleForComponent('period-text', 'helper:period-text', {
  integration: true
});

// Replace this with your real tests.
test('it renders', function(assert) {
  this.set('inputValue', '1');

  this.render(hbs`{{period-text inputValue}}`);

  assert.equal(this.$().text().trim(), '1 Month period');
});
