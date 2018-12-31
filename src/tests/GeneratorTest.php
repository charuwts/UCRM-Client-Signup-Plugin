<?php
declare(strict_types=1);
namespace Ucsp\Test;

chdir(__DIR__);
define("PROJECT_PATH", __DIR__);
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


  /**
  * @test
  **/
  public function addStripeToClientIfClientExists() {
    $mock = $this->getMockBuilder(Generator::class)
                 ->setMethods(['addStripe'])
                 ->getMock();
    $this->assertFalse($mock->isAccessGranted());
  }
}