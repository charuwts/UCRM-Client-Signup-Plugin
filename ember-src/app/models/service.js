import DS from 'ember-data';
const { attr } = DS;
import { computed } from '@ember/object';

export default DS.Model.extend({
  activeFrom: attr('date'), 
  invoicingStart: attr('date'), 
  invoicingPeriodStartDay: attr('date'), 
  price: attr('number'),
  
  // servicePlan: belongsTo('service-plan'), 
  servicePlan: attr(), 
  servicePlanComputedId: computed('servicePlan', function() {
    return parseInt(this.get('servicePlan.id'));
  }), 
  servicePlanPeriodComputedId: computed('servicePlan', function() {
    return parseInt(this.get('servicePlan.periods.firstObject.id'));
  }), 
  serviceSurcharges: attr(),

  discountType: attr('number'), // {0: 'No discount', 1: 'Percentage discount', 2: 'Fixed discount'}
  discountValue: attr('number'), 
  discountFrom: attr('date'), 
  discountTo: attr('date'), 
  discountInvoiceLabel: attr('string'), 
});
