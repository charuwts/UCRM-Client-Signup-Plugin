<?php
declare(strict_types=1);
namespace Ucsp\Test;

use PHPUnit\Framework\TestCase;
use \Ucsp\Interpreter;

class InterpreterTest extends TestCase {
  protected function setUp() {
    Interpreter::setDataUrl(__DIR__.'/../data/');
    Interpreter::setFrontendKey('test_key');
    $this->Interpreter = new Interpreter();
  }
  protected function tearDown() {
    unset($this->Interpreter);
  }

  /**
  * @test
  * @covers Interpreter::getFrontendKey
  **/
  public function expectFrontendKey() {
    $key = Interpreter::getFrontendKey();
    $this->assertSame('test_key', $key);
  }

  /**
  * @test
  * @expectedException UnexpectedValueException
  * @expectedExceptionCode 404
  **/
  public function expectExceptionOnGetEndpointThatIsNotWhiteListed() {
    $payload = json_encode(["frontendKey" => "test_key", "api" => ["type" => "GET", "endpoint" => "countries/22/states/invalid", "data" => "test"]]);
    $this->Interpreter->run($payload);
  }

  
  /**
  * @test
  * @expectedException UnexpectedValueException
  * @expectedExceptionCode 404
  **/
  public function expectExceptionOnPostEndpointThatIsNotWhiteListed() {
    $payload = json_encode(["frontendKey" => "test_key", "api" => ["type" => "POST", "endpoint" => "clients/1", "data" => "test"]]);
    $this->Interpreter->run($payload);
  }

  /**
  * @test
  **/
  public function expectFalseOnEmptyPayload() {
    $payload = json_encode([]);
    $this->Interpreter->run($payload);

    $this->assertSame(false, $this->Interpreter->isReady(), 'Interpreter should not be ready if payload is empty');
  }

  /**
  * @test
  **/
  public function expectFalseOnEmptyFrontendKey() {
    $payload = json_encode(["frontendKey" => ""]);
    $this->Interpreter->run($payload);

    $this->assertSame(false, $this->Interpreter->isReady(), 'Interpreter should not be ready if frontendKey is empty');
  }
  /**
    Providers!
  **/
  public function runProvider() {
    $exceptionClass = \UnexpectedValueException::class;
    $payload = json_encode(["frontendKey" => "invalid_key", "api" => ["type" => "GET", "endpoint" => "countries", "data" => []]]);
    $payload2 = json_encode(["frontendKey" => "test_key"]);
    $payload3 = json_encode(["frontendKey" => "test_key", "api" => ["type" => "GET", "data" => "test"]]);
    $payload4 = json_encode(["frontendKey" => "test_key", "api" => ["endpoint" => "clients", "data" => "test"]]);

    return [
      'invalid key' => [$payload, $exceptionClass, 'frontendKey is invalid'],
      'invalid data' => [$payload2, $exceptionClass, 'data is invalid', 400],
      'invalid endpoint' => [$payload3, $exceptionClass, 'endpoint is not set', 400],
      'invalid type' => [$payload4, $exceptionClass, 'type is not set', 400]
    ];
  }

  /**
  * @test  
  * @covers Interpreter::run
  * @dataProvider runProvider
  **/
  public function expectExceptionsForRun($payload, $exceptionClass, $exceptionMessage, $exceptionCode = false) {
    $this->expectException($exceptionClass);
    $this->expectExceptionMessage($exceptionMessage);
    if ($exceptionCode) {
      $this->expectExceptionCode($exceptionCode);
    }
    $this->Interpreter->run($payload);
  }


  /**
  * @test
  * @covers Interpreter::run
  **/
  public function expectSuccessfullPayloadOnGet() {
    // Mock Api Response
    $mock_results = [['id' => 19, 'name' => 'Afghanistan', 'code' => 'AF']];
    $mock = $this->getMockBuilder(Interpreter::class)
                 ->setMethods(['get'])
                 ->getMock();
    $mock->method('get')->will($this->returnValue($mock_results));

    // Pass in payload and run mock
    $payload = json_encode(["frontendKey" => "test_key", "api" => ["type" => "GET", "endpoint" => "countries"]]);
    $mock->run($payload);

    // Success
    $this->assertSame($mock->getResponse(), json_encode($mock_results), 'Payload should return successfully');
  }

  /**
  * @test
  * @covers Interpreter::run
  **/
  public function expectSuccessfullPayloadOnPost() {
    // Mock Api Response
    $mock = $this->getMockBuilder(Interpreter::class)
                 ->setMethods(['post'])
                 ->getMock();
    $mock->method('post')->will($this->returnValue(null));

    // Pass in payload and run mock
    $payload = json_encode([
        "frontendKey" => "test_key",
        "api" => [
            "type" => "POST",
            "endpoint" => "clients",
            "data" => [
              "clientType" => 1,
              "isLead" => false,
              "firstName" => "Torg",
              "lastName" => "Lastname",
              "street1" => "Street 1",
              "city" => "City",
              "countryId" => null,
              "stateId" => null,
              "zipCode" => "12345",
              "username" => "brandon+tests6@charuwts.com",
              "contacts" => [
                [
                  "isBilling" => true,
                  "isContact" => true,
                  "email" => "brandon+tests6@charuwts.com",
                  "phone" => "22222222222",
                  "name" => "Torg Lastname"
                ]
              ],
              "attributes" => [
                [
                  "value" => "brandon+tests6@charuwts.com",
                  "customAttributeId" => 39
                ],
                [
                  "value" => "14,66",
                  "customAttributeId" => 37
                ],
                [
                  "value" => "tok_1DqMmRDvjcKFitZMNqyiid3u",
                  "customAttributeId" => 36
                ]
              ]
            ]
          ]
    ]);

    $mock->run($payload);
    // $this->Interpreter->run($payload);

    $this->assertSame(200, $mock->getCode());
  }



}
