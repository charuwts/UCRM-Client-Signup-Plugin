import Component from '@ember/component';

export default Component.extend({
  classNames: ['service-plan', 'col-md-4', 'col-sm-6', 'px-2', 'py-2'],
  actions: {
    selectServicePlan(plan, periodId, periodPrice, period) {
      this.set('model.servicePlan', {
        id: plan.id,
        name: plan.name,
        periodId: periodId,
        periodPrice: periodPrice,
        period: period
      });
      this.set('model.servicePlanPeriodId', periodId);
    }, 
  }
});
