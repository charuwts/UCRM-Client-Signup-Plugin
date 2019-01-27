<?php
declare(strict_types=1);
namespace Ucsp\Test;

use PHPUnit\Framework\TestCase;
use \Ucsp\Webhook;


        // "uuid": "29756f3f-fd97-47a1-9f0d-d2ca776cafc9",
class WebhookTest extends TestCase {
  public function validateWebhookProvider() {
    return [
      "fake_uuid" => [false, "29756f33-fd97-47a1-9f0d-d2ca776cafc9", "Should be False when Uuid Not Found"],
      "empty_uuid" => [false, "", "Should be False When Uuid is Empty"]
    ];
  }

  /**
  * @test
  * @dataProvider validateWebhookProvider
  * @covers validateWebhook
  **/
  public function validateWebhookTest($expected, $string, $message) {
    $handler = new Webhook();
    $this->assertSame($expected, $handler->validateWebhook($string), $message);
  }


  public function handleWebhookProvider() {
    return [
      'valid_payload' => ['{
        "uuid": "29756f3f-fd97-47a1-9f0d-d2ca776cafc9",
        "changeType": "insert",
        "entity": "client",
        "entityId": 155,
        "eventName": "client.add",
        "extraData": {
          "entity": {
            "id": 155,
            "isLead": false,
            "clientType": 1,
            "street1": "Street 1",
            "city": "City",
            "countryId": 249,
            "stateId": 6,
            "zipCode": "12345",
            "invoiceAddressSameAsContact": true,
            "organizationId": 3,
            "registrationDate": "2019-01-09T07:42:14-0800",
            "isActive": false,
            "firstName": "Torg",
            "lastName": "Lastname",
            "username": "test@test.com",
            "contacts": [
              {
                "id": 155,
                "clientId": 155,
                "email": "test@test.com",
                "phone": "22222222222",
                "name": "Torg Lastname",
                "isBilling": true,
                "isContact": true,
                "types": [
                  {
                    "id": 1,
                    "name": "Billing"
                  },
                  {
                    "id": 2,
                    "name": "General"
                  }
                ]
              }
            ],
            "attributes": [
              {
                "id": 92,
                "clientId": 155,
                "customAttributeId": 39,
                "name": "Ucsp Form Email",
                "key": "ucspFormEmail",
                "value": "test@test.com"
              },
              {
                "id": 93,
                "clientId": 155,
                "customAttributeId": 37,
                "name": "Ucsp Service Data",
                "key": "ucspServiceData",
                "value": "14,66"
              },
              {
                "id": 94,
                "clientId": 155,
                "customAttributeId": 36,
                "name": "Ucsp Gateway Token",
                "key": "ucspGatewayToken",
                "value": "tok_1DqjC6DvjcKFitZMw8eKazRX"
              }
            ],
            "accountBalance": 0,
            "accountCredit": 0,
            "accountOutstanding": 0,
            "currencyCode": "USD",
            "organizationName": "torg",
            "bankAccounts": [],
            "tags": [],
            "avatarColor": "#e53935",
            "isArchived": false
          }
        }
      }']
    ];
  }
  public function noMatchingAttributeProvider() {
    return [
      'payload_missing_ucspServiceData' => ['{
        "uuid": "29756f3f-fd97-47a1-9f0d-d2ca776cafc9",
        "changeType": "insert",
        "entity": "client",
        "entityId": 155,
        "eventName": "client.add",
        "extraData": {
          "entity": {
            "id": 155,
            "isLead": false,
            "clientType": 1,
            "attributes": [
              {
                "id": 92,
                "clientId": 155,
                "customAttributeId": 39,
                "name": "Ucsp Form Email",
                "key": "ucspFormEmail",
                "value": "test@test.com"
              },
              {
                "id": 94,
                "clientId": 155,
                "customAttributeId": 36,
                "name": "Ucsp Gateway Token",
                "key": "ucspGatewayToken",
                "value": "tok_1DqjC6DvjcKFitZMw8eKazRX"
              }
            ]
          }
        }
      }']
    ];
  }

  /**
  * @test
  * @covers validateWebhook
  * @dataProvider handleWebhookProvider
  **/
  public function expectTrueIfSuccess($payload) {
    $mock = $this->getMockBuilder(Webhook::class)
                 ->setMethods(['get'])
                 ->getMock();
    $mock->method('get')->will($this->returnValue(json_decode($payload, true)));

    $this->assertTrue($mock->validateWebhook("29756f3f-fd97-47a1-9f0d-d2ca776cafc9"));
  }

  /**
  * @test
  * @covers webhookHandler
  **/
  public function webhookHandlerShouldReturnFalseIfNotValidated() {
    $mock = $this->getMockBuilder(Webhook::class)
                 ->setMethods(['validateWebhook'])
                 ->getMock();
    $mock->method('validateWebhook')->will($this->returnValue(false));

    $this->assertFalse($mock->handleWebhook(''));
  }

  /**
  * @test
  * @dataProvider noMatchingAttributeProvider
  * @covers webhookHandler
  **/
  public function expectFalseIfNoMatchingAttributeDetails($payload) {
    $mock = $this->getMockBuilder(Webhook::class)
                 ->setMethods(['validateWebhook'])
                 ->getMock();
    $mock->method('validateWebhook')->will($this->returnValue(true));

    $this->assertFalse($mock->handleWebhook($payload));
  }

  /**
  * @test
  * @dataProvider handleWebhookProvider
  * @covers webhookHandler
  **/
  public function expectSuccessfulServiceCreation($payload) {
    $mock = $this->getMockBuilder(Webhook::class)
                 ->setMethods(['validateWebhook'])
                 ->getMock();
    $mock->method('validateWebhook')->will($this->returnValue(true));
    // $mock->method('post')->will($this->returnValue(null));

    $this->assertTrue($mock->handleWebhook($payload));
  }
}