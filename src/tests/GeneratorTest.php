<?php
declare(strict_types=1);
namespace Ucsp\Test;

chdir(__DIR__);

require_once(PROJECT_PATH.'/../includes/custom-exceptions.php');

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
        ['id' => 1, 'name' => 'Ucsp Stripe User Id', 'key' => 'UcspStripeUserId', 'attributeType' => 'client'],
        ['id' => 2, 'name' => 'Ucsp Form Email', 'key' => 'UcspFormEmail', 'attributeType' => 'client'],
        ['id' => 3, 'name' => 'Ucsp Form Step', 'key' => 'UcspFormStep', 'attributeType' => 'client'],
        ['id' => 4, 'name' => 'Ucsp Errors', 'key' => 'UcspErrors', 'attributeType' => 'client']
      ]]
    ];
  }

  /**
  * @test
  * @dataProvider nonMatchingCustomAttributeProvider
  **/
  public function missingCustomAttributes($mock_results) {
    $mock = $this->getMockBuilder(Generator::class)
                 ->setMethods(['get'])
                 ->getMock();
    $mock->method('get')->will($this->returnValue($mock_results));

    $this->assertFalse($mock->customAttributesExists('UcspGatewayToken'));
  }

  /**
  * @test
  * @dataProvider customAttributeProvider
  **/
  public function customAttributesExists($mock_results) {
    $mock = new Generator();

    $mock = $this->getMockBuilder(Generator::class)
                 ->setMethods(['get'])
                 ->getMock();
    $mock->method('get')->will($this->returnValue($mock_results));

    $this->assertTrue($mock->customAttributesExists('UcspGatewayToken'));
  }

  /**
  * @test
  **/
  public function createCustomAttributes() {
  }



}