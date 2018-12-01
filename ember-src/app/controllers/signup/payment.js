import Controller from '@ember/controller';
import { computed } from '@ember/object';
import { inject as service } from '@ember/service';
import ENV from "../../config/environment";

export default Controller.extend({
  ajax: service(),
  stripev3: service(),
  
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

  actions: {
    submit(stripeElement) {
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
              pluginAppKey: ENV.APP.pluginAppKey,
              stripeInfo: {
                token: token.token.id,
              },
              client: {
                "firstName": this.get('model.client.firstName'),
                "lastName": this.get('model.client.lastName'),
                "street1": this.get('model.client.street1'),
                "street2": this.get('model.client.street2'),
                "city": this.get('model.client.city'),
                "countryId": this.get('model.client.countryId'),
                "stateId": this.get('model.client.stateId'),
                "zipCode": this.get('model.client.zipCode'),
                "username": this.get('model.client.email'),
                "contacts": [
                  {
                    isBilling: true,                    
                    isContact: true,
                    email: this.get('model.client.email'),
                    phone: this.get('model.client.phone'),
                    name: this.get('model.client.firstName') + ' ' + this.get('model.client.lastName')
                  }
                ],
                // "attributes": [
                //   {
                //     value: String(this.get('model.client.agreedToTAC')),
                //     customAttributeId: 2,
                //   }
                // ]

              },
              service: {
                "servicePlanId": this.get('model.servicePlan.id'),
                "servicePlanPeriodId": this.get('model.servicePlanPeriodId'),
              },
              // job: this.get('model.job')
            } 
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
          // order.set('stripeToken', token.token.id);

        } else {
          this.set('pending', false);
        }
      });
    },
  },
});
