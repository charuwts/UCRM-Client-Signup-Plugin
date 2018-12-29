<?php
declare(strict_types=1);
namespace Ucsp\Test;

chdir(__DIR__);
define("PROJECT_PATH", __DIR__);
require_once(PROJECT_PATH.'/../includes/custom-exceptions.php');


use PHPUnit\Framework\TestCase;
use \Ucsp\Config;

class ConfigTest extends TestCase {
  // protected function setUp() {
  //   $user = null;
  // }
  // protected function tearDown() {
  //   unset($this->Config);
  // }

  /**
  * @test
  * @expectedException IsNotAdminException
  * @expectedExceptionCode 403
  **/
  public function expectAccessNotGrantedIfUserDoesNotHavePermission() {
    $mock = $this->getMockBuilder(Config::class)
                 ->setMethods(['hasPermission'])
                 ->getMock();
    $this->assertFalse($mock->isAccessGranted());
  }
  /**
  * @test
  **/
  public function expectAccessGrantedWithPermission() {
    $mock = $this->getMockBuilder(Config::class)
                 ->disableOriginalConstructor()
                 ->setMethods(['hasPermission'])
                 ->getMock();
    $mock->expects($this->any())->method('hasPermission')->will($this->returnValue(true));
    $mock->checkPermissions();
    $this->assertTrue($mock->isAccessGranted());
  }

  /**
  * @test
  **/
  public function expectArray() {
    $mock = $this->getMockBuilder(Config::class)
                 ->disableOriginalConstructor()
                 ->setMethods(['hasPermission'])
                 ->getMock();
    $mock->method('hasPermission')->will($this->returnValue(true));

    $result = $mock->viewFile('service-filters');
    
    $this->assertInternalType('array', $result);
  }

  /**
  * @test
  **/
  public function writeToFile() {
    $mock = $this->getMockBuilder(Config::class)
                 ->disableOriginalConstructor()
                 ->setMethods(['hasPermission', 'isAccessGranted'])
                 ->getMock();

    $mock->method('hasPermission')->will($this->returnValue(true));
    $mock->method('isAccessGranted')->will($this->returnValue(true));

    $result = $mock->updateFile('service-filters', ['test' => 'array']);
    
    $this->assertSame($result, ['test' => 'array']);
  }


}