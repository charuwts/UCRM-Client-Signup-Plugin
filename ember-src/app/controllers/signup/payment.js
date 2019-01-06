import Controller from '@ember/controller';
import { computed } from '@ember/object';
import { inject as service } from '@ember/service';
import ENV from "../../config/environment";

export default Controller.extend({
  ajax: service(),
  ucrmGenerator: service(),
  stripev3: service(),
  collectPayment: computed('ajax', function() {
    if (ENV.APP.collectPayment === 'yes') {
      return true;
    } else {
      return false;
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

  success: computed('ucrmGenerator.success', function() {
    if (this.get('ucrmGenerator.success')) {
      this.transitionToRoute('signup.complete');
    }
  }),

  actions: {
    createClient(client) {
      this.set('failure', false);
      this.set('success', false);
      this.set('errors', false);

      return client.validate().then(({ validations }) => {
        this.set('pending', true);
        this.set('processing', true);
        
        let $isLead = ENV.APP.isLead === 'no' ? false : true;

        if (validations.get('isValid')) {
          
          let response = this.get('ajax').post(ENV.APP.host, {
            headers: {
              "Content-Type": 'application/json'
            },
            data: {
              frontendKey: ENV.APP.frontendKey,
              api: {
                type: 'POST',
                endpoint: 'clients',
                data: {
                  "clientType": 1,
                  "isLead": $isLead,
                  "firstName": client.get('firstName'),
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
                  ]
                  // "attributes": [
                    // {
                    //   value: String(client.agreedToTAC),
                    //   customAttributeId: 2,
                    // }
                  // ]
                },
              }
            } 
          }).catch((resp) => {
            if ((resp.payload !== undefined) && (resp.payload !== null)) {
              this.set('errors', resp.payload.errors);
              this.set('errorMessage', resp.payload.message);
            }

            this.set('pending', false);
            this.set('failure', true);
          }).then(() => {
            if (this.get('failure') !== true) {
              this.set('success', true);
            }
            this.set('pending', false);
          });

          this.set('processing', false);
          this.set('response', response);
          this.get('response', response);
        }
      });

    },


    submit(stripeElement, client) {
      this.set('pending', true);
      this.set('processing', true);
      this.set('failure', false);
      this.set('errors', false);

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
                  "clientType": 1,
                  "isLead": $isLead,
                  "firstName": client.get('firstName'),
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
                      value: String(token.token.id),
                      customAttributeId: null,
                    }
                  ]
                },
              }
            } 
              // service: {
              //   "servicePlanId": this.get('model.servicePlan.id'),
              //   "servicePlanPeriodId": this.get('model.servicePlanPeriodId'),
              // }
          }).catch((resp) => {
            if (resp.payload !== undefined) {
              if (resp.payload.redirect === true) {
                this.set('failure', false);
                this.transitionToRoute('signup.account', { queryParams: { expired: true }});
              } else {
                this.set('errorMessage', resp.payload.message);
                this.set('errors', resp.payload.errors);
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
