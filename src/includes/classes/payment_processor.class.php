<?php

class PaymentProcessor extends UcrmHandler {
  function __construct($gateway) {
    self::$selected_gateway = $gateway;
  }

  private static $selected_gateway;

  private function checkForUnpaidInvoices() {
    $invoice = json_decode($this->getUnpaidInvoices());
  }

}