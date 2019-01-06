<?php
declare(strict_types=1);
namespace Ucsp\Test;

require_once(__DIR__.'/../includes/custom-exceptions.php');

use PHPUnit\Framework\TestCase;
use \Ucsp\Generator;

class GeneratorTest extends TestCase {
  protected function setUp() {
    $this->Generator = new Generator();
  }

  protected function tearDown() {
    unset($this->Generator);
  }

  public function nonMatchingCustomAttributeProvider() {
      return [
          "isNull" => [null],
          "empty array" => [[]],
          "with some attributes" => [[['id' => 1, 'name' => 'Gateway Customer ID', 'key' => 'GatewayCustomerId', 'attributeType' => 'client'], ['id' => 3, 'name' => 'test', 'key' => 'test', 'attributeType' => 'invoice']]]
      ];
  }

  public function customAttributeProvider() {
    return [
      [[
        ['id' => 1, 'name' => 'Ucsp Gateway User Id', 'key' => 'ucspGatewayCustomerId', 'attributeType' => 'client'],
        ['id' => 2, 'name' => 'Ucsp Form Email', 'key' => 'ucspFormEmail', 'attributeType' => 'client'],
        ['id' => 3, 'name' => 'Ucsp Form Step', 'key' => 'ucspFormStep', 'attributeType' => 'client'],
        ['id' => 4, 'name' => 'Ucsp Errors', 'key' => 'ucspErrors', 'attributeType' => 'client']
      ]]
    ];
  }

  /**
  * @test
  * @covers Generator::customAttributesExists
  * @dataProvider nonMatchingCustomAttributeProvider
  **/
  public function missingCustomAttributes($mock_results) {
    $mock = $this->getMockBuilder(Generator::class)
                 ->setMethods(['get', 'updateFile'])
                 ->getMock();
    $mock->method('get')->will($this->returnValue($mock_results));

    $this->assertInternalType('array', $mock->customAttributesExists());
  }

  /**
  * @test
  * @covers Generator::customAttributesExists
  * @dataProvider customAttributeProvider
  **/
  public function customAttributesExists($mock_results) {
    $mock = $this->getMockBuilder(Generator::class)
                 ->setMethods(['get', 'updateFile'])
                 ->getMock();
    $mock->method('get')->will($this->returnValue($mock_results));

    $this->assertTrue($mock->customAttributesExists());
  }

  /**
  * @test
  * @covers Generator::createCustomAttributes
  **/
  public function expectFalseIfCustomAttributesExist() {
    $mock = $this->getMockBuilder(Generator::class)
                 ->setMethods(['customAttributesExists'])
                 ->getMock();
    $mock->method('customAttributesExists')->will($this->returnValue(true));

    $missing = $mock->createCustomAttributes();
    $this->assertFalse($missing, 'should not create attributes if they exist');
  }

  /**
  * @test
  * @covers Generator::createCustomAttributes
  **/
  public function expectCreateCustomAttributes() {
    $mock_get_attributes = [['id' => 3, 'name' => 'test', 'key' => 'test', 'attributeType' => 'invoice'], ['id' => 4, 'name' => 'agreedToTAC', 'key' => 'agreedtotac', 'attributeType' => 'client'], ['id' => 22, 'name' => 'Ucsp Gateway Customer Id', 'key' => 'ucspGatewayCustomerId', 'attributeType' => 'client'], ['id' => 23, 'name' => 'Ucsp Form Email', 'key' => 'ucspFormEmail', 'attributeType' => 'client'], ['id' => 24, 'name' => 'Ucsp Form Step', 'key' => 'ucspFormStep', 'attributeType' => 'client'], ['id' => 25, 'name' => 'Ucsp Errors', 'key' => 'ucspErrors', 'attributeType' => 'client']];
    $mock_results = ['Ucsp Gateway Customer Id' => 'ucspGatewayCustomerId', 'Ucsp Form Email' => 'ucspFormEmail', 'Ucsp Form Step' => 'ucspFormStep', 'Ucsp Errors' => 'ucspErrors'];
    $mock = $this->getMockBuilder(Generator::class)
                 ->setMethods(['customAttributesExists', 'post', 'get'])
                 ->getMock();
    $mock->method('customAttributesExists')->will($this->onConsecutiveCalls($mock_results, $mock_results, true));
    $mock->method('post')->will($this->returnValue($mock_results));
    $mock->method('get')->will($this->returnValue($mock_get_attributes));

    $result = $mock->createCustomAttributes();
    $this->assertTrue($result);
  }



}