import Controller from '@ember/controller';

export default Controller.extend({
  actions: {
    proceed() {
      this.transitionToRoute('signup.payment');
    },
    resetServicePlan() {
      this.set('model.servicePlan', null);
      this.set('model.servicePlanPeriodId', null);
    }
  }
});
