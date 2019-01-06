import Route from '@ember/routing/route';

export default Route.extend({
  queryParams: {
    expired: {
      refreshModel: true
    }
  },
  model(params) {
    if (params.expired === true) {
      this.set('controller.expired', true);
    }
    return this.modelFor('signup');
  },
});
