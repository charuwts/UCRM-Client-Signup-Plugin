import Controller from '@ember/controller';
import { computed } from '@ember/object';
import { inject as service } from '@ember/service';
import ENV from "../../config/environment";

export default Controller.extend({
  ajax: service(),
  stripev3: service(),
  collectPayment: computed('ajax', function() {
    if (ENV.APP.collectPayment === 'yes') {
      return true;
    } else {
      return false;
    }
  }),
  showAddress: computed('model.client.{street1,city,zipCode}', function() {
    if (this.get('model.client.street1') || this.get('model.client.city') || this.get('model.client.zipCode')) {
      return true;
    } else {
      return false;
    }
  }),
  cityComma: computed('model.client.{city,zipCode}', function() {
    if (this.get('model.client.city') && this.get('model.client.zipCode')) {
      return true;
    } else {
      return false;
    }
  }),
  CustomFieldOneValue: computed('model.pluginConfig.{CustomFieldOneValue,CustomFieldOneOptions}', function() {
    if (this.get('model.pluginConfig.CustomFieldOneValue')) {
      return this.get('model.pluginConfig.CustomFieldOneValue');
    } else {
      if ((this.get('model.pluginConfig.CustomFieldOneOptions')) && (!this.get('model.pluginConfig.CustomFieldOnePlaceholder'))) {
        return this.get('model.pluginConfig.CustomFieldOneOptions').split(",")[0];
      }
    }
  }),
  CustomFieldTwoValue: computed('model.pluginConfig.{CustomFieldTwoValue,CustomFieldTwoOptions}', function() {
    if (this.get('model.pluginConfig.CustomFieldTwoValue')) {
      return this.get('model.pluginConfig.CustomFieldTwoValue');
    } else {
      if ((this.get('model.pluginConfig.CustomFieldTwoOptions')) && (!this.get('model.pluginConfig.CustomFieldTwoPlaceholder'))) {
        return this.get('model.pluginConfig.CustomFieldTwoOptions').split(",")[0];
      }
    }
  }),
  CustomFieldThreeValue: computed('model.pluginConfig.{CustomFieldThreeValue,CustomFieldThrOptions}', function() {
    if (this.get('model.pluginConfig.CustomFieldThreeValue')) {
      return this.get('model.pluginConfig.CustomFieldThreeValue');
    } else {
      if ((this.get('model.pluginConfig.CustomFieldThrOptions')) && (!this.get('model.pluginConfig.CustomFieldThrPlaceholder'))) {
        return this.get('model.pluginConfig.CustomFieldThrOptions').split(",")[0];
      }
    }
  }),
  CustomFieldFourValue: computed('model.pluginConfig.{CustomFieldFourValue,CustomFieldFouOptions}', function() {
    if (this.get('model.pluginConfig.CustomFieldFourValue')) {
      return this.get('model.pluginConfig.CustomFieldFourValue');
    } else {
      if ((this.get('model.pluginConfig.CustomFieldFouOptions')) && (!this.get('model.pluginConfig.CustomFieldFouPlaceholder'))) {
        return this.get('model.pluginConfig.CustomFieldFouOptions').split(",")[0];
      }
    }
  }),
  options: computed('stripev3', function() {
    return {
      hidePostalCode: true,
      style: {
        base: {
          color: '#32325D',
          fontSize: '26px',
          fontSmoothing: 'antialiased',

          '::placeholder': {
            color: '#DDD',
          },
          ':-webkit-autofill': {
            color: '#e39f48',
          },
        },
      }
    }
  }), 

  isLead: computed('ENV.APP.isLead', function() {
    return ENV.APP.isLead === 'no' ? false : true;
  }),

  planIds: computed('model.{servicePlanId,servicePlanPeriodId}', function() {
    if (this.get('model.servicePlanId') && this.get('model.servicePlanPeriodId')) {
      return this.get('model.servicePlanId') + ',' + this.get('model.servicePlanPeriodId');
    }
  }),

  clientType(client) {
    if (client.get('companyName')) {
      return 2;
    } else {
      return 1;
    }
  },

  actions: {
    createClient(client) {
      this.set('failure', false);
      this.set('success', false);
      this.set('errors', false);

      return client.validate().then(({ validations }) => {
        this.set('pending', true);
        
        if (validations.get('isValid')) {
          
          this.get('ajax').post(ENV.APP.host, {
            headers: {
              "Content-Type": 'application/json'
            },
            data: {
              frontendKey: ENV.APP.frontendKey,
              api: {
                type: 'POST',
                endpoint: 'clients',
                data: {
                  "clientType": this.clientType(client),
                  "organizationId": parseInt(this.get('model.pluginConfig.organizationId')),
                  "isLead": this.get('isLead'),
                  "firstName": client.get('firstName'),
                  "companyName": client.get('companyName'),
                  "companyContactFirstName": client.get('companyContactFirstName'),
                  "companyContactLastName": client.get('companyContactLastName'),
                  "companyWebsite": client.get('companyWebsite'),
                  "lastName": client.get('lastName'),
                  "street1": client.get('street1'),
                  "street2": client.get('street2'),
                  "city": client.get('city'),
                  "countryId": client.get('countryId'),
                  "stateId": client.get('stateId'),
                  "zipCode": client.get('zipCode'),
                  "username": client.get('email'),
                  "contacts": [
                    {
                      isBilling: true,                    
                      isContact: true,
                      email: client.get('email'),
                      phone: client.get('phone'),
                      name: client.get('firstName') + ' ' + client.get('lastName')
                    }
                  ],
                  "attributes": [
                    {
                      value: String(client.get('email')),
                      customAttributeId: this.get('model.pluginConfig.formEmailAttributeId')
                    },
                    {
                      value: this.get('planIds'),
                      customAttributeId: this.get('model.pluginConfig.serviceDataAttributeId')
                    },
                    {
                      value: this.get('CustomFieldOneValue'),
                      customAttributeId: parseInt(this.get('model.pluginConfig.CustomFieldOneAttrId'))
                    },
                    {
                      value: this.get('CustomFieldTwoValue'),
                      customAttributeId: parseInt(this.get('model.pluginConfig.CustomFieldTwoAttrId'))
                    },
                    {
                      value: this.get('CustomFieldThreeValue'),
                      customAttributeId: parseInt(this.get('model.pluginConfig.CustomFieldThreeAttrId'))
                    },
                    {
                      value: this.get('CustomFieldFourValue'),
                      customAttributeId: parseInt(this.get('model.pluginConfig.CustomFieldFourAttrId'))
                    }
                  ]
                },
              }
            } 
          }).catch((resp) => {
            if ((resp.payload !== undefined) && (resp.payload !== null)) {
              this.set('errors', JSON.parse(resp.payload).errors);
              this.set('errorMessage', JSON.parse(resp.payload).message);
            }

            this.set('pending', false);
            this.set('failure', true);
          }).then(() => {
            if (this.get('failure') !== true) {
              this.transitionToRoute('signup.complete');
            }
            this.set('pending', false);
          });

          this.set('pending', false);
        }
      });

    },


    submit(stripeElement, client) {
      this.set('pending', true);
      this.set('failure', false);
      this.set('errors', false);

      let attrsExist = true;
      if (!this.get('model.pluginConfig.formEmailAttributeId')) {
        attrsExist = false;
      }
      if (!this.get('model.pluginConfig.serviceDataAttributeId')) {
        attrsExist = false;
      }
      if (!this.get('model.pluginConfig.tokenAttributeId')) {
        attrsExist = false;
      }
      if (attrsExist === false) {
        this.set('errorMessage', 'Plugin custom attributes missing');
        this.set('errors', true);
        this.set('pending', false);
        this.set('failure', true);
        return;
      } 

      let stripe = this.get('stripev3');
      stripe.createToken(stripeElement).then((token) => {
        if (token.token !== undefined) {
          this.get('ajax').post(ENV.APP.host, {
            headers: {
              "Content-Type": 'application/json'
            },
            data: {
              frontendKey: ENV.APP.frontendKey,
              api: {
                type: 'POST',
                endpoint: 'clients',
                data: {
                  "clientType": this.clientType(client),
                  "organizationId": parseInt(this.get('model.pluginConfig.organizationId')),
                  "isLead": this.get('isLead'),
                  "firstName": client.get('firstName'),
                  "companyName": client.get('companyName'),
                  "companyContactFirstName": client.get('companyContactFirstName'),
                  "companyContactLastName": client.get('companyContactLastName'),
                  "companyWebsite": client.get('companyWebsite'),
                  "lastName": client.get('lastName'),
                  "street1": client.get('street1'),
                  "street2": client.get('street2'),
                  "city": client.get('city'),
                  "countryId": client.get('countryId'),
                  "stateId": client.get('stateId'),
                  "zipCode": client.get('zipCode'),
                  "username": client.get('email'),
                  "contacts": [
                    {
                      isBilling: true,                    
                      isContact: true,
                      email: client.get('email'),
                      phone: client.get('phone'),
                      name: client.get('firstName') + ' ' + client.get('lastName')
                    }
                  ],
                  "attributes": [
                    {
                      value: String(client.get('email')),
                      customAttributeId: this.get('model.pluginConfig.formEmailAttributeId')
                    },
                    {
                      value: this.get('planIds'),
                      customAttributeId: this.get('model.pluginConfig.serviceDataAttributeId')
                    },
                    {
                      value: String(token.token.id),
                      customAttributeId: this.get('model.pluginConfig.tokenAttributeId')
                    },
                    {
                      value: this.get('CustomFieldOneValue'),
                      customAttributeId: parseInt(this.get('model.pluginConfig.CustomFieldOneAttrId'))
                    },
                    {
                      value: this.get('CustomFieldTwoValue'),
                      customAttributeId: parseInt(this.get('model.pluginConfig.CustomFieldTwoAttrId'))
                    },
                    {
                      value: this.get('CustomFieldThreeValue'),
                      customAttributeId: parseInt(this.get('model.pluginConfig.CustomFieldThreeAttrId'))
                    },
                    {
                      value: this.get('CustomFieldFourValue'),
                      customAttributeId: parseInt(this.get('model.pluginConfig.CustomFieldFourAttrId'))
                    }

                  ]
                }
              }
            }
          }).catch((resp) => {
            if (resp.payload !== undefined) {
              if (resp.payload.redirect === true) {
                this.set('failure', false);
                this.transitionToRoute('signup.account', { queryParams: { expired: true }});
              } else {
                this.set('errorMessage', JSON.parse(resp.payload).message);
                this.set('errors', JSON.parse(resp.payload).errors);
              }
            }
            this.set('pending', false);
            this.set('failure', true);
          }).then(() => {
            if (this.get('failure') !== true) {
              this.transitionToRoute('signup.complete');
            }
            this.set('pending', false);
          });

        } else {
          this.set('pending', false);
        }
      });
    },
  },
});
