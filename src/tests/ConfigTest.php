<?php
declare(strict_types=1);
namespace Ucsp\Test;

require_once(__DIR__.'/../includes/custom-exceptions.php');


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
  * @covers Config->isAccessGranted
  **/
  public function expectAccessNotGrantedIfUserDoesNotHavePermission() {
    $mock = $this->getMockBuilder(Config::class)
                 ->setMethods(['hasPermission'])
                 ->getMock();
    $this->assertFalse($mock->isAccessGranted());
  }
  /**
  * @test
  * @covers Config->isAccessGranted
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
  * @covers Config->viewFile
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
  * @covers Config->writeToFile
  **/
  public function writeToFile() {
    $mock = $this->getMockBuilder(Config::class)
                 ->disableOriginalConstructor()
                 ->setMethods(['hasPermission', 'isAccessGranted'])
                 ->getMock();

    $mock->method('hasPermission')->will($this->returnValue(true));
    $mock->method('isAccessGranted')->will($this->returnValue(true));

    $result = $mock->writeToFile('service-filters', ['test' => 'array']);
    
    $this->assertTrue($result);
  }

  /**
  * @test
  * @covers Config->updateFile
  **/
  public function updateFile() {
    $mock = $this->getMockBuilder(Config::class)
                 ->disableOriginalConstructor()
                 ->setMethods(['hasPermission', 'isAccessGranted', 'autoUpdates', 'writeToFile', 'viewFile'])
                 ->getMock();

    $mock->method('hasPermission')->will($this->returnValue(true));
    $mock->method('isAccessGranted')->will($this->returnValue(true));
    $mock->method('autoUpdates')->will($this->returnValue(['test' => 'array']));
    $mock->method('writeToFile')->will($this->returnValue(true));
    $mock->method('viewFile')->will($this->returnValue(['test' => 'array']));

    $result = $mock->updateFile('service-filters', ['test' => 'array']);
    
    $this->assertSame($result, ['test' => 'array']);
  }

}