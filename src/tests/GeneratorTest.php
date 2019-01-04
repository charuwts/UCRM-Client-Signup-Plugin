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
          "with some attributes" => [[['id' => 1, 'name' => 'Stripe Customer ID', 'key' => 'stripeCustomerId', 'attributeType' => 'client'], ['id' => 3, 'name' => 'test', 'key' => 'test', 'attributeType' => 'invoice']]]
      ];
  }

  public function customAttributeProvider() {
    return [
      [[
        ['id' => 1, 'name' => 'Ucsp Stripe User Id', 'key' => 'UcspStripeCustomerId', 'attributeType' => 'client'],
        ['id' => 2, 'name' => 'Ucsp Form Email', 'key' => 'UcspFormEmail', 'attributeType' => 'client'],
        ['id' => 3, 'name' => 'Ucsp Form Step', 'key' => 'UcspFormStep', 'attributeType' => 'client'],
        ['id' => 4, 'name' => 'Ucsp Errors', 'key' => 'UcspErrors', 'attributeType' => 'client']
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
                 ->setMethods(['get'])
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
                 ->setMethods(['get'])
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
    $mock_results = ['Ucsp Stripe Customer Id' => 'UcspStripeCustomerId', 'Ucsp Form Email' => 'UcspFormEmail', 'Ucsp Form Step' => 'UcspFormStep', 'Ucsp Errors' => 'UcspErrors'];
    $mock = $this->getMockBuilder(Generator::class)
                 ->setMethods(['customAttributesExists', 'post'])
                 ->getMock();
    $mock->method('customAttributesExists')->will($this->returnValue($mock_results));
    $mock->method('post')->will($this->returnValue($mock_results));

    $missing = $mock->createCustomAttributes();
    $this->assertSame($mock_results, $missing, 'should result in an array');
  }



}