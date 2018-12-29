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
    $mock->method('hasPermission')->will($this->returnValue(false));
    $this->assertFalse($mock->isAccessGranted());
  }
  /**
  * @test
  * @expectedException IsNotAdminException
  * @expectedExceptionCode 403
  **/
  public function expectAccessGrantedWithPermission() {
    $mock = $this->getMockBuilder(Config::class)
                 ->setMethods(['hasPermission'])
                 ->getMock();
    $mock->method('hasPermission')->will($this->returnValue(true));
    $this->assertTrue($mock->isAccessGranted());
  }

  /**
  * @test
  **/
  public function expectEmptyFile() {
    $mock = $this->getMockBuilder(Config::class)
                 ->disableOriginalConstructor()
                 ->setMethods(['hasPermission'])
                 ->getMock();
    $mock->method('hasPermission')->will($this->returnValue(true));

    $file = $mock->viewFile('service-filters');
    
    $this->assertSame([], $file);
  }


}