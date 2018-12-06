<?php
/* 
 * Copyright © 2018 · Charuwts, LLC
 * All rights reserved.
 * You may not redistribute or modify the Software of Charuwts, LLC, and you are prohibited from misrepresenting the origin of the Software.
 * 
 */
namespace UCSP;

class PaymentProcessor extends UcrmHandler {
  function __construct($gateway) {
    self::$selected_gateway = $gateway;
  }

  private static $selected_gateway;


  /**
   * # gateway method id 
   *
   * @return integer
   * 
   */
  private static function selectedGatewayMethodId() {
    if (self::$selected_gateway == 'STRIPE') {
      return 6;
    }    
  }

  private function checkForUnpaidInvoices() {
    return json_decode($this->getUnpaidInvoices());
    // $invoices = json_decode($this->getUnpaidInvoices());
    // log_event('invoices', print_r($invoices, true), 'test');
    // log_event('duedate', $invoice[0]->dueDate, 'test');
  }


  /**
   * # gatewayCharge 
   * # determine gateway and handle charge accordingly
   *
   * @param string $client_id
   * @param number $amount
   * @param string $currency
   * 
   * @return object
   * 
   */
  private function gatewayCharge($client_id, $amount, $currency) {
    
    if (self::$selected_gateway == 'STRIPE') {
      $gateway_handler = new StripeHandler();
      $charge = $gateway_handler->chargeCustomer($client_id, $amount, $currency);
    }
    if (!empty($charge->amount)) {
      return ["id" => $charge->id, "amount" => $charge->amount];
    } else {
      return [];  
    }
  }

  public function processPayments() {
    // ## Get all invoices marked as unpaid
    $invoices = $this->checkForUnpaidInvoices();
    $invoice_array = [];

    // ## Loop invoices and handle if due
    foreach($invoices as $invoice) {
      
      // ## Setup dates
      $now = new DateTime();
      $dueDate = strtotime($invoice->dueDate);
      $invoice_due_date = new DateTime("@{$dueDate}");

      // ## If due date is past
      if ($invoice_due_date <= $now) {
        
        // ## Charge Client invoice total
        $gateway_charge = $this->gatewayCharge($invoice->clientId, $invoice->total, $invoice->currencyCode);

        if (!empty($gateway_charge)) {
          $ucrm_handler = new UcrmHandler();
          $selected_gateway = self::$selected_gateway;
          // ## https://ucrm.docs.apiary.io/#reference/payments/payments/post
          $ucrm_handler->createPayment(
            $gateway_charge['id'], // # providerPaymentId
            $gateway_charge['amount'], // # amount
            $invoice->id, // # invoiceId
            $invoice->clientId, // # clientId
            "Auto Invoice Payment - charged via {$selected_gateway}",  // # note
            self::selectedGatewayMethodId(), // # method 
            $invoice->currencyCode // # currencyCode
          ); 
          // ## push ID onto array
          $invoice_array[] = $invoice->id;

        }
      }
    }

  }

}