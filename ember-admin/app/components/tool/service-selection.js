import Component from '@ember/component';

export default Component.extend({
  classNames: ['service-selection', 'col-auto', 'px-2', 'py-2'],
  radioValue: true,
  actions: {
    toggle() {
      this.toggleProperty('radioValue');
    }
  }
});
