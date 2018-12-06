<?php
/* 
 * Copyright © 2018 · Charuwts, LLC
 * All rights reserved.
 * You may not redistribute or modify the Software of Charuwts, LLC, and you are prohibited from misrepresenting the origin of the Software.
 * 
 */
namespace UCSP;

class UcrmHandler extends UcrmApi {
  public function buildServices($payload) {
    // ## Setup
    $payload_decoded = json_decode($payload);
    $this->validateObject($payload_decoded, true);
    $ucrm_handler = new UcrmHandler;

    // ## Create Client
    $client = $ucrm_handler->createClient($payload_decoded->client);
    if (empty($client->errors)) {
      $date = new \DateTime();
      $date = date('c', strtotime("+1 months"));
      $service = $ucrm_handler->createService($payload_decoded->service, $client->id, $date);

      $ucrm_handler->createAdminTicket('New Signup', $client->id, "{$client->firstName} {$client->lastName} has signed up.");
      $this->setResponse('Services Created', 200);
    } else {
      $this->setResponse('Client failed', 400);
    }
  }
   ## Create Client
   # @param array $client
   # @return object
  public function createClient($client, $json_response=false) {

    $content = array(
      "firstName" => (empty($client->firstName)) ? null : $client->firstName,
      "lastName" => (empty($client->lastName)) ? null : $client->lastName,
      "street1" => (empty($client->street1)) ? null : $client->street1,
      "city" => (empty($client->city)) ? null : $client->city,
      "zipCode" => (empty($client->zipCode)) ? null : $client->zipCode,
      "username" => (empty($client->username)) ? null : $client->username,
      "contacts" => (empty($client->contacts)) ? null : $client->contacts,
    );
    $this->validateObject($content);

    # Optional Params
    $content['countryId'] = (empty($client->countryId)) ? null : $client->countryId;
    $content['stateId'] = (empty($client->stateId)) ? null : $client->stateId;
    $content['street2'] = (empty($client->street2)) ? null : $client->street2;
    // $content['isLead'] = (empty(Config::$LEAD)) ? false : Config::$LEAD;

    $response = UsageHandler::guzzle('POST', '/clients', $content);
    
    if ($json_response) {
      echo json_response($response['message'], 200, true);
      exit();
    } else {
      return json_decode($response['message']);
    }
  }

   ## Get Current User
   # @return object
  public static function isAdmin() {
    $response = UsageHandler::retrieveCurrentUser(Config::PLUGIN_URL());
    if (empty($response['error'])) {
      if ($response['isClient'] !== true && $response['permissions']['system/plugins'] == 'edit') {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

   ## Set Custom Attribute Value
   # @param string $client_id
   # @param string $customer_id
   # @return JSON string
  public function setCustomAttributeValue($client_id, $customer_id) {
    $content = [
      "attributes" => [
        [
          "value" => $customer_id,
          "customAttributeId" => (int)Config::$CUSTOM_ATTRIBUTE_ID
        ]
        
      ]
    ];
    $endpoint = "/clients/{$client_id}";

    $response = UsageHandler::guzzle('PATCH', $endpoint, $content);
    return $response['message'];
  }

  /**
   * # Get Custom Attribute Value
   *
   * @param array $attributes
   * 
   * @return string || false if no success
   *
   */
  public function getCustomAttributeValue($attributes) {

    $value = false;
    foreach($attributes as $attr) {
      if ($attr->customAttributeId == Config::$CUSTOM_ATTRIBUTE_ID) {
        $value = $attr->value;
        break;
      }
    }
    return $value;

  }

  /**
   * # Get Invoices
   * 
   * @param array $status
   *
   * @return JSON string
   *
   */
  public function getUnpaidInvoices() {
    // ## 1 = unpaid, 2 = partially paid
    $endpoint = "/invoices?statuses[0]=1";
    $response = UsageHandler::guzzle('GET', $endpoint);
    return $response['message'];
  }

  /**
   * # Create Invoice
   *
   * @param string $label
   * @param integer $price
   * @param integer $client_id
   * 
   * @return object
   * 
   */
  public function createInvoice($label, $price, $client_id, $note='') {
    $content = array(
      'items' => array(
        array(
          'label' => $label,
          'price' => $price,
          'quantity' => 1,
        )
      ),
      'notes' => $note,
      // 'invoiceTemplateId' => 1001,
      'adminNotes' => 'Generated by UCRM Client Signup Plugin'
    );

    $response = UsageHandler::guzzle('POST', "/clients/{$client_id}/invoices", $content);
    return json_decode($response['message']); // # Invoice
  }

  /**
   * # Send Invoice
   * 
   * @param integer $invoice_id
   *
   * @return JSON string
   *
   */
  public function sendInvoice($invoice_id) {
    $endpoint = "/invoices/{$invoice_id}/send";
    // $this->response = $this->ucrmPatch('Send Receipt', $endpoint);
    $response = UsageHandler::guzzle('PATCH', $endpoint);
    return $response['message'];
  }


  /**
   * # Create Payment
   *
   * @param integer $charge_id
   * @param integer $charge_amount // Stripe amount is in cents
   * @param integer $invoice_id
   * @param integer $client_id
   * @param string  $note
   * @param integer $method
   * @param string  $currency
   * 
   * @return object
   * 
   */
  public function createPayment($charge_id, $charge_amount, $invoice_id, $client_id, $note='', $method=6, $currency='USD') {
    // ## Convert stripe cents to dollars for UCRM
    $amount = $charge_amount/100;
    
    // ## Webhook Payments are applied automatically, so don't include invoiceIds
    if ($invoice_id == "auto_invoice") {
      $content = [
        "clientId" => intval($client_id),
        "method" => $method,
        "amount" => $amount,
        "currencyCode" => $currency,
        "note" => $note,
        "applyToInvoicesAutomatically" => true,
      ];
    } else {
      $content = [
        "clientId" => intval($client_id),
        "method" => $method,
        "amount" => $amount,
        "currencyCode" => $currency,
        "note" => $note,
        "invoiceIds" => [$invoice_id],
      ];
    }

    $response = UsageHandler::guzzle('POST', '/payments', $content);
    return json_decode($response['message']); // # Payment
  }

  /**
   * # Create Admin Ticket
   *
   * @param string $subject
   * @param integer $client_id
   * @param integer $group_id
   * 
   * @return object
   * 
   */
  public function createAdminTicket($subject, $client_id, $body='') {
    $content = [
      'subject' => $subject,
      'clientId' => $client_id,
      'status' => 0,
      'public' => false,
      'activity' => [
        [
          'public' => false,
          'comment' => [
            'body' => $body,
          ]
        ]
      ]
    ];




    $response = UsageHandler::guzzle('POST', "/ticketing/tickets", $content);
    return json_decode($response['message']); // # Ticket
  }


  /**
   * # Get Clients
   *
   * @return json
   *
   */
  public function getClients() {    
    $endpoint = "/clients";
    $response = UsageHandler::guzzle('GET', $endpoint);
    $json_decoded = json_decode($response['message']);

    if (!empty($json_decoded[0]->id)) {
      return $json_decoded;
    } else {
      return false;
    }
  }

  /**
   * # Get Client
   *
   * @return json
   *
   */
  public function getClient($client_id) {    
    $endpoint = "/clients/{$client_id}";
    $response = UsageHandler::guzzle('GET', $endpoint);
    $json_decoded = json_decode($response['message']);

    if (!empty($json_decoded->id)) {
      return $json_decoded;
    } else {
      return false;
    }
  }

  ## Get Service Plans
  # @return json
  public static function getServicePlans() {
    $endpoint = "/service-plans";
    $response = UsageHandler::guzzle('GET', $endpoint);
    // $this->response = $response['message'];
    return json_response($response['message'], 200, true);
  }

  ## Get Service Plan Filters
  # @return json
  public static function getServicePlanFilters() {
    $service_json_file = PROJECT_PATH.'/data/services_filter.json'; 
    $data = [];

    if (file_exists($service_json_file)) {
      
      $jsonString = file_get_contents($service_json_file);
      $contents = empty($jsonString) ? [] : json_decode($jsonString, true);

      if (count($contents) > 0) {
        return json_response($jsonString, 200, true);
      } else {
        return self::createServicePlanFilters();
      }
    } else {
      return self::createServicePlanFilters();
    }
  }

  ## Set Service Plan Filters
  # @return json
  public static function updateServicePlanFilters($service_plans = []) {
    if (self::isAdmin()) {
      $service_json_file = PROJECT_PATH.'/data/services_filter.json'; 
      $service_plans = (!empty($service_plans)) ? $service_plans : self::getServicePlanFilters();
      $json_services = json_decode($service_plans);

      $data = [];
      
      foreach ($json_services as $service_plan) {
        $filter_formatt = [];
        $filter_formatt["id"] = $service_plan->id;
        $filter_formatt["display"] = !empty($service_plan->display) ? $service_plan->display : false;
        $filter_formatt["name"] = $service_plan->name;
        $filter_formatt["periods"] = $service_plan->periods;

        $data[] = $filter_formatt;
      }

      file_put_contents($service_json_file, json_encode($data));

      if (file_exists($service_json_file)) {
        return json_response(json_encode($data), 200, true);
      } else {
        return json_response(json_encode([]), 200, true);
      }
    } else {
      throw new ApiException('Permission Denied', 403);
    }
  }

  ## Set Service Plan Filters
  # @return json
  public static function createServicePlanFilters() {
    if (self::isAdmin()) {
      $service_plans = self::getServicePlans();
      return self::updateServicePlanFilters($service_plans);
    } else {
      throw new ApiException('Permission Denied', 403);
    }
  }

  /**
   * # Get Countries
   *
   * @return json
   *
   */
  public function getCountries() {
    $endpoint = "/countries";
    $response = UsageHandler::guzzle('GET', $endpoint);
    $this->response = $response['message'];
    return $response['message'];
  }

  /**
   * # Get States by Country
   *
   * @return json
   *
   */
  public function getStatesByCountry($country_id) {
    $endpoint = "/countries/{$country_id}/states";
    $response = UsageHandler::guzzle('GET', $endpoint);
    $this->response = $response['message'];
    return $response['message'];
  }

  /**
   * # Get Client Services
   *
   */
  public function getClientServices($client_id) {
    $endpoint = "/clients/{$client_id}/services";
    $response = UsageHandler::guzzle('GET', $endpoint);

    return $response['message'];
  }

  /**
   * # Create Service Plan
   *
   * @param object $product
   * @param object $plan
   * 
   * @return object
   * 
   */
  public function createServicePlan($product, $plan) {
    // ## If not zero-decimal currency, decimal
    // if (ZERO_DECIMAL === false) {
    $amount = $plan->amount / 100;
    // }

    // ## Combine product name and plan ID for unique name
    $name = empty($plan->nickname) ? $plan->id : $plan->nickname;
    $name = $product->name . ' - ' . $name;

    $content = [
      "name" => $plan->nickname,
      "invoiceLabel" => $product->name,
      "periods" => [
        [
          "period" => $plan->interval_count,
          "price" => $amount,
          "enabled" => true
        ]
      ]
    ];
    
    $response = UsageHandler::guzzle('POST', '/service-plans', $content);
    return json_decode($response['message']); // # service-plan
  }


  /**
   * # Create service
   *
   * @param array $service
   * @param string $client_id
   * 
   * @return object
   *
   */
  public function createService($service, $client_id, $date) {
    $required_content = array(
      "activeFrom" => $date,
      "invoicingStart" => $date,
      "servicePlanId" => $service->servicePlanId,
      "servicePlanPeriodId" => $service->servicePlanPeriodId,
    );
    
    $this->validateObject($required_content);

    $response = UsageHandler::guzzle('POST', "/clients/{$client_id}/services", $required_content);
    return json_decode($response['message']); // # Service
  }


  /**
   * # Send Payment Receipt
   * 
   * @param integer $payment_id
   *
   * @return JSON string
   *
   */
  public function sendReceipt($payment_id) {
    $endpoint = "/payments/{$payment_id}/send-receipt";
    // $this->response = $this->ucrmPatch('Send Receipt', $endpoint);
    $response = UsageHandler::guzzle('PATCH', $endpoint);
    return $response['message'];
  }

  /**
   * ## Handle UCRM webhooks
   * 
   * @param string $payload // JSON
   *
   *
   */
  public function handleWebhook($payload) {
    $payload_decoded = $this->validateWebhook($payload);

    // ## If auto invoicing is enabled
    if (AUTO_INVOICE === true) {

      // ## If Webhook is an invoice and it's been edited  
      if (($payload_decoded->entity == 'invoice') && ($payload_decoded->changeType == 'edit')) {
        $endpoint = "/invoices/{$payload_decoded->entityId}";
        // $invoice_resp = $this->ucrmGet($endpoint);
        $response = UsageHandler::guzzle('GET', $endpoint);
        $invoice_resp = json_decode($response['message']);

        // ## If invoice is paid, send 'receipt'
        if (($invoice_resp->status == 3) && (strpos($invoice_resp->notes, 'installation fee') === false)) {
          $this->sendInvoice($payload_decoded->entityId);
          $this->setResponse('Invoice Sent', 200);
        } else {
          $this->setResponse('Invoice edited', 200);
        }
      }

    } else {
      $this->setResponse('Webhook received', 200);
    }

  }


}