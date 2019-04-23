<?php
declare(strict_types=1);
namespace Ucsp;

class Stripe {
  public function __construct() {
    $this->api = \Ubnt\UcrmPluginSdk\Service\UcrmApi::create();
  }

  public function __destruct() {
      unset($this->api);
  }

  public function post($endpoint, $data = []) {
    return $this->api->post($endpoint, $data);
  }
  public function patch($endpoint, $data = []) {
    return $this->api->patch($endpoint, $data);
  }

  public function get($endpoint, $data = []) {
    return $this->api->get($endpoint, $data);
  }

  public function log_event($log_title, $event, $type='log') {
    $current_time = date(DATE_ATOM);
    $message = "\n[{$current_time}][{$type}]  - [#{$log_title}] \n";
    $message .= $event;
    $message .= "\n[{$current_time}][{$type}] - [/{$log_title}] \n";
    
    file_put_contents(PROJECT_PATH.'/data/log.log', $message, FILE_APPEND);
  }

  /**
   * # Catch Stripe exceptions when making API request
   *
   * @param arguments $args // args can be whatever the anonymous function needs, if multiple args are needed pass in an array
   * @param function $function
   *
   */
  public function StripeTry($args, $function) {
    try {
      return $function($args);
    } catch(\Stripe\Error\Card $e) {
      // $body = $e->getJsonBody();
      // $err  = $body['error'];
      $this->log_event("Card declined", $e);
    } catch (\Stripe\Error\RateLimit $e) {
      $this->log_event("Too many requests made to the API too quickly", $e);
    } catch (\Stripe\Error\InvalidRequest $e) {
      $this->log_event("Invalid parameters were supplied to Stripe's API", $e);
    } catch (\Stripe\Error\Authentication $e) {
      $this->log_event("Authentication with Stripe's API failed", $e);
    } catch (\Stripe\Error\ApiConnection $e) {
      $this->log_event("Network communication with Stripe failed", $e);
    } catch (\Stripe\Error\Base $e) {
      $this->log_event("Stripe Error", $e);
    } catch (Exception $e) {
      $this->log_event("Stripe Exception", $e);
    }
  }

  public function createCustomer($client_id, $token, $email) {
    if (!empty($client_id) && !empty($token)) {
      $response = $this->StripeTry(["client_id" => $client_id, "token" => $token, "email" => $email], function($array) {
        return \Stripe\Customer::create([
          'source' => $array['token'],
          'email' => $array['email'],
          'metadata' => [
            'ucrm_client_id' => $array['client_id']
          ]
        ], [
          "idempotency_key" => $array['client_id'].'_'.$array['token']
        ]);
      });

      if (!empty($response->id)) {

        $Generator = new Generator();
        $tokenId = $Generator->getAttributeId('ucspGatewayToken');
        $attrId = $Generator->getAttributeId('ucspGatewayCustomer');
        $this->patch('clients/'.$client_id, [
          "attributes" => [
            [
              "value" => $response->id,
              "customAttributeId" => $attrId
            ],
            [
              "value" => null,
              "customAttributeId" => $tokenId
            ]
          ]
        ]);        
      }

    }
  }


  /**
   * ## Stripe Charge
   * 
   * @param string $payload // JSON
   *
   *
   */
  public function chargeCustomer($invoice) {
    $CurrencyHandler = new \Ucsp\CurrencyHandler();
    $Generator = new \Ucsp\Generator();

    $client = $this->get('clients/'.$invoice['clientId']);
    $customer_id = $Generator->getAttribute($client['attributes'], 'ucspGatewayCustomer');

    if ($client) {
      if ($customer_id) {
        
        // ## Convert to cents if not zero decimal currency
        if ($CurrencyHandler->notZeroDecimal($invoice['currencyCode'])) {
          $amount = ($invoice['total'] - $invoice['amountPaid']) * 100;
        } else {
          $amount = $invoice['total'] - $invoice['amountPaid'];
        }

        $charge = $this->StripeTry(["customer_id" => $customer_id, "amount" => $amount, "currency" => $invoice['currencyCode'], "invoice_id" => $invoice['id']], function($array) {
          return \Stripe\Charge::create([
            'amount' => $array['amount'],
            'currency' => $array['currency'],
            'description' => 'UCRM Charge',
            'customer' => $array['customer_id']
          ], [
            "idempotency_key" => "payment_amount_". $array['amount'] ."_id_". $array['invoice_id']
          ]);
        });
        if ($charge['paid'] == true) {
          return $charge;
        } else {
          $this->log_event('Charge Failed', "Customer ID: {$customer_id}");
          return false;
        }
      } else {
        $this->log_event('Ucsp Gateway Customer (Stripe Customer ID) not set on client', "Client ID: {$invoice['clientId']}");
        return false;
      }
    } else {
      $this->log_event('Client not found', "Client ID: {$invoice['clientId']}");
      return false;
    }
  }

  public function processPayments() {
    file_put_contents(PROJECT_PATH.'/data/plugin.log', "\n== Start Processing Invoices ==\n");

    // ## Get all invoices marked as unpaid
    if (empty($invoices)) {
      $invoices = $this->get('invoices', ['statuses' => [1,2]]);
    }

    $invoice_array = [];

    // ## Loop invoices and handle if due
    foreach($invoices as $invoice) {
      
      // ## Setup dates
      $now = new \DateTime();
      $newTime = $now->add(new \DateInterval('P5D'));
      $dueDate = strtotime($invoice['dueDate']);
      $invoice_due_date = new \DateTime("@{$dueDate}");

      // ## If due date is past
      if ($invoice_due_date <= $newTime) {

        try {
          // ## Charge Client invoice total
          $gateway_charge = $this->chargeCustomer($invoice);
          
          $CurrencyHandler = new \Ucsp\CurrencyHandler();
          if ($CurrencyHandler->notZeroDecimal($invoice['currencyCode'])) {
            $amount = $gateway_charge['amount'] / 100;
          } else {
            $amount = $gateway_charge['amount'];
          }

          if ($gateway_charge !== false) {
            // ## https://ucrm.docs.apiary.io/#reference/payments/payments/post
            $content = [
              "clientId" => intval($invoice['clientId']),
              "method" => 99, // custom
              "providerName" => 'Stripe', // Required in case of Custom method.
              "providerPaymentId" => $gateway_charge['id'], // Required in case of Custom method.
              "amount" => $amount,
              "currencyCode" => $invoice['currencyCode'],
              "note" => 'Auto Invoice Payment - charged via Stripe',
              "invoiceIds" => [$invoice['id']]
            ];
            
            // ## https://ucrm.docs.apiary.io/#reference/payments/payments/post
            $this->post('payments', $content);            
            $this->post('client-logs', [
              "message" => "Payment was processed via Stripe",
              "clientId" => $invoice['clientId']
            ]);
          }
        } catch (\Exception $e) {
          $this->log_event('Processing Invoice - '.$invoice['id'], $e->getMessage());
          throw $e;
        }

        // ## push ID onto array
        $invoice_array[] = $invoice->id;


      // if ($invoice_due_date <= $newTime)
      } else {
        $this->log_event('Processing Invoice - '.$invoice['id'], 'Invoice payment will be processed 5 days before due date '.$newTime->format('Y-m-d'));
      }
    }
    file_put_contents(PROJECT_PATH.'/data/plugin.log', "\n== Finished Processing Invoices ==\n", FILE_APPEND);
  }

  /**
   * ## Stripe Webhook Validation
   * # https://stripe.com/docs/webhooks/signatures
   * 
   * @param string $payload // JSON
   *
   * Exits upon failure
   *
   * @return boolean // true
   */
  // protected function validateWebhook($payload) {
  //   $event = null;
  //   $sig_header = $_SERVER["HTTP_STRIPE_SIGNATURE"];
  //   try {
  //     $event = \Stripe\Webhook::constructEvent(
  //       $payload, $sig_header, self::$endpoint_secret
  //     );
  //   } catch(\UnexpectedValueException $e) {
  //     $this->log_event('Stripe error', 'invalid payload', 'error'); // Invalid payload
  //     exit();
  //   } catch(\Stripe\Error\SignatureVerification $e) {
  //     $this->setResponse($e->getMessage(), 400);
  //     $this->log_event('Stripe error', 'invalid signature', 'error'); // Invalid signature
  //     exit();
  //   }
  //   return true;
  // }

  /**
   * ## Handle stripe webhooks
   * 
   * @param string $payload // JSON
   *
   *
   */
  // public function handleWebhook($payload) {
    
  //   if (\UCSP\Config::$SAVE_PAYMENT_SOURCE) {
  //     file_put_contents(PROJECT_PATH.'/data/plugin.log', '', LOCK_EX);
  //     // ## Validate
  //     $this->validateWebhook($payload);
  //     // ## Setup
  //     $webhook_payload_decoded = json_decode($payload);
  //     // $ucrm_handler = new UcrmHandler;
  //     // $this->log_event('stripe webhook', print_r($webhook_payload_decoded, true));
  //     // $this->log_event('stripe webhook source id', $webhook_payload_decoded->data->object->source->id);
  //   } 

  //   // ## Tell UCRM about invoice payments
  //   // if ($webhook_payload_decoded->type == "invoice.payment_succeeded") {
  //   //   // ## Assign required webhook variables
  //   //   $charge_id = $webhook_payload_decoded->data->object->charge;
  //   //   $amount_paid = $webhook_payload_decoded->data->object->amount_paid;
      
  //   //   // # Use customer ID to get UCRM Client ID
  //   //   $client_id = $ucrm_handler->getClientId($webhook_payload_decoded->data->object->customer);

  //   //   // ## Make payment and return success if greater then 0
  //   //   if ($amount_paid > 0) {
  //   //     $ucrm_handler->createPayment($charge_id, $amount_paid, "auto_invoice", $client_id);
  //   //     $this->setResponse('Payment Created', 200);
  //   //   } 
  //   // }
  // }


}