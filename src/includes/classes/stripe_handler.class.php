<?php

class StripeHandler extends StripeApi {

  public function buildServices($payload) {
    // ## Setup
    $payload_decoded = json_decode($payload);
    $this->validateForm($payload_decoded, $payload_decoded->pluginAppKey);
    $ucrm_api = new UcrmApi;

    // ## Create Client
    $client = $ucrm_api->createClient($payload_decoded->client);

    // ## Check Client
    if (empty($client->errors)) {

      // ## Create Stripe Customer
      $customer = $this->StripeTry(["payload_decoded" => $payload_decoded, "client" => $client], function($array) {
        return \Stripe\Customer::create([
          'source' => $array['payload_decoded']->stripeInfo->token,
          'email' => $array['payload_decoded']->client->username,
          'metadata' => [
            'ucrm_client_id' => $array['client']->id
          ]
        ]);
      });
      // ## Charge for Installation Fee
      $charge = $this->StripeTry(["customer_id" => $customer->id, "payload_decoded" => $payload_decoded], function($array) {
        $fee_amount = $array['payload_decoded']->stripeInfo->feeAmount * 100; // Stripe's amount is in cents
        return \Stripe\Charge::create([
          'amount' => $fee_amount,
          'currency' => 'usd',
          'description' => 'Installation Charge',
          'customer' => $array['customer_id']
        ]);
      });

      // ## Check if Charge was successful
      if ($charge->paid) {
        // ## Set Stripe Customer ID to client's unique userIdent
        $ucrm_api->setStripeClientId($client->id, $customer->id);

        // ## Create Service
        $date = new DateTime($payload_decoded->job->date);
        $date = $date->format("F j, Y, g:ia");

        // ## Notify Admin
        $ucrm_api->createAdminTicket('New Signup', $client->id, 1, "{$client->firstName} {$client->lastName} has signed up.");
        
        // ## Schedule Subscription
        $subscription = $this->StripeTry(["payload_decoded" => $payload_decoded, "customer" => $customer], function($array) {
          return \Stripe\Subscription::create([
            "customer" => $array['customer']->id,
            "coupon" => $array['payload_decoded']->stripeInfo->couponId,
            "items" => [
              [
                "plan" => $array['payload_decoded']->stripeInfo->planId,
              ],
            ],
            'trial_end' => strtotime("+24 days", strtotime($array['payload_decoded']->service->invoicingStart)),
          ]);
        });

        $this->setResponse('Services Created', 200);
      } else {
        $this->setResponse('Payment Failed', 400);
      }
    } else {
      $this->setResponse('Client failed', 400);
    }
  }

  /**
   * ## Get Pricing Plans
   *
   */
  public function getPricingPlans() {
    log_event('plan', 'test');
    $plans = $this->StripeTry('', function() {
      return \Stripe\Plan::all(array("limit" => 10, "active" => true));
    });

    log_event('plan', print_r($plans, true));
    foreach ($plans->autoPagingIterator() as $plan) {
      log_event('plan', print_r($plan, true));
    }

  }

  /**
   * ## Handle stripe webhooks
   * 
   * @param string $payload // JSON
   *
   *
   */
  public function handleWebhook($payload) {
    // ## Validate
    $this->validateWebhook($payload);
    // ## Setup
    $webhook_payload_decoded = json_decode($payload);
    $ucrm_api = new UcrmApi;
    
    // ## Tell UCRM about invoice payments
    if ($webhook_payload_decoded->type == "invoice.payment_succeeded") {
      // ## Assign required webhook variables
      $charge_id = $webhook_payload_decoded->data->object->charge;
      $amount_paid = $webhook_payload_decoded->data->object->amount_paid;
      
      // # Use customer ID to get UCRM Client ID
      $client_id = $ucrm_api->getClientId($webhook_payload_decoded->data->object->customer);

      // ## Make payment and return success if greater then 0
      if ($amount_paid > 0) {
        $ucrm_api->createPayment($charge_id, $amount_paid, "auto_invoice", $client_id);
        $this->setResponse('Payment Created', 200);
      } 
    }
  }


}