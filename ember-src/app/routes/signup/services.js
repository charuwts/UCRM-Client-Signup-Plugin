import Route from '@ember/routing/route';

export default Route.extend({
  beforeModel() {
    let model = this.modelFor('signup');
    // model.client.validate().then(({ validations }) => {
    //   if (!validations.get('isValid')) {
    //     this.transitionTo('signup.account');
    //   }
    // });
    if (model.servicePlans.length === 0) {
      this.transitionTo('signup.payment');
    }
  },

  model() {
    return this.modelFor('signup')
  }
});