import DS from 'ember-data';
import { validator, buildValidations } from 'ember-cp-validations';
const { attr } = DS;

const Validations = buildValidations({
  firstName: [
    validator('presence', true),
    validator('length', {
      min: 3,
    })
  ],
  lastName: [
    validator('presence', true),
    validator('length', {
      min: 3,
    })
  ],
  email: [
    validator('presence', true),
    validator('format', { type: 'email' })
  ]
});

export default DS.Model.extend(Validations, {
  firstName: attr('string'),
  lastName: attr('string'),
  contacts: attr(),
  street1: attr('string'),
  street2: attr('string'),
  zipCode: attr('string'),
  city: attr('string'),

  email: attr('string'),
  phone: attr('string'),

  stateId: attr('number', {defaultValue: null}),
  countryId: attr('number', {defaultValue: null}),
});