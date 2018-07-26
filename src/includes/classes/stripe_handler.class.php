<?php
/* 
 * Copyright © 2018 · Charuwts, LLC
 * All rights reserved.
 * You may not redistribute or modify the Software of Charuwts, LLC, and you are prohibited from misrepresenting the origin of the Software.
 * 
 */

class StripeHandler extends StripeApi {

  public function buildServices($payload) {
    // ## Setup
    $payload_decoded = json_decode($payload);
    $this->validateForm($payload_decoded, $payload_decoded->pluginAppKey);
    $ucrm_handler = new UcrmHandler;

    // ## Create Client
    $client = $ucrm_handler->createClient($payload_decoded->client);

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

      // ## Check if Card was successfully set
      if (!empty($customer->default_source)) {
        // ## Set Stripe Customer ID to client's custom attribute
        $ucrm_handler->setCustomAttributeValue($client->id, $customer->id);

        // ## Create Service
        $date = new DateTime();
        // ## Set active from and invoicing start to +1 month to give time to adjust service invoice dates manually
        $date = date('c', strtotime("+1 months"));
        $service = $ucrm_handler->createService($payload_decoded->service, $client->id, $date);

        // ## Notify Admin
        $ucrm_handler->createAdminTicket('New Signup', $client->id, "{$client->firstName} {$client->lastName} has signed up.");
        
        UsageHandler::is($client->id, $service->id);

        $this->setResponse('Services Created', 200);
      } else {
        $this->setResponse('Payment Failed', 400);
      }
    } else {
      $this->setResponse('Client failed', 400);
    }
  }

  /**
   * ## Stripe Charge
   * 
   * @param string $payload // JSON
   *
   *
   */
  public function chargeCustomer($client_id, $amount, $currency) {
    $ucrm_handler = new UcrmHandler();
    $client = $ucrm_handler->getClient($client_id);
    $customer_id = $ucrm_handler->getCustomAttributeValue($client->attributes);

    if ($client !== false) {
      if ($customer_id !== false) {

        $currency = strtolower($currency);
        
        // ## Convert to cents if not zero decimal currency
        if (parent::notZeroDecimal($currency)) {
          $amount = $amount * 100; 
        }

        $charge = $this->StripeTry(["customer_id" => $customer_id, "amount" => $amount, "currency" => $currency], function($array) {
          return \Stripe\Charge::create([
            'amount' => $array['amount'],
            'currency' => $array['currency'],
            'description' => 'UCRM Charge',
            'customer' => $array['customer_id']
          ]);
        });
        if ($charge->paid == true) {
          return $charge;
        } else {
          log_event('Charge Failed', "Customer ID: {$customer_id}");
          return false;
        }
      } else {
        log_event('Custom Attr not set on client', "Client ID: {$client_id}");
        return false;
      }
    } else {
      log_event('Client not found', "Client ID: {$client_id}");
      return false;
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
    $ucrm_handler = new UcrmHandler;
    
    // ## Tell UCRM about invoice payments
    if ($webhook_payload_decoded->type == "invoice.payment_succeeded") {
      // ## Assign required webhook variables
      $charge_id = $webhook_payload_decoded->data->object->charge;
      $amount_paid = $webhook_payload_decoded->data->object->amount_paid;
      
      // # Use customer ID to get UCRM Client ID
      $client_id = $ucrm_handler->getClientId($webhook_payload_decoded->data->object->customer);

      // ## Make payment and return success if greater then 0
      if ($amount_paid > 0) {
        $ucrm_handler->createPayment($charge_id, $amount_paid, "auto_invoice", $client_id);
        $this->setResponse('Payment Created', 200);
      } 
    }
  }


}