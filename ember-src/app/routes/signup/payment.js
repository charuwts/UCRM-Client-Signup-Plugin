import Route from '@ember/routing/route';

export default Route.extend({
  beforeModel() {
    let model = this.modelFor('signup');
    model.client.validate().then(({ validations }) => {
      if (!validations.get('isValid')) {
        this.transitionTo('signup.account');
      }
    });
    if (model.client.get('countryId') === undefined) {
      model.client.set('country', null);
      model.client.set('state', null);
    }
  },

  model() {
    return this.modelFor('signup')
  }
});