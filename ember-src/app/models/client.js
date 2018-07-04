import DS from 'ember-data';
import { validator, buildValidations } from 'ember-cp-validations';
import { computed } from '@ember/object';
import ENV from "../config/environment";

const { attr } = DS;
var Validations;
if (ENV.APP.useCountrySelect === 'TRUE') {
  Validations = buildValidations({
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
    ],
    phone: [
      validator('presence', true),
    ],
    countryId: [
      validator('presence', true),
    ]
  });
} else {
  Validations = buildValidations({
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
    ],
    phone: [
      validator('presence', true),
    ]
  });
}

export default DS.Model.extend(Validations, {
  firstName: attr('string'),
  lastName: attr('string'),
  contacts: attr(),
  street1: attr('string'),
  // street2: attr('string'),
  zipCode: attr('string'),
  city: attr('string'),

  email: attr('string'),
  phone: attr('string'),

  // state: belongsTo('state'),
  state: attr(),
  country: attr(),

  agreedToTAC: attr('boolean', {defaultValue: false}),

  stateId: computed('state', function() {
    return this.get('state.id');
  }),
  // // country: belongsTo('country'),
  countryId: computed('country', function() {
    return this.get('country.id');
  }),
});