<?php
namespace PPHP\tests\tools\patterns\singleton;

use \PPHP\tools\patterns\singleton as singleton;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class SingletonTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers singleton\Singleton::getInstance
   * @covers singleton\TSingleton::getInstance
   */
  public function testGetInstance(){
    $instance = TestSingleton::getInstance();
    $this->assertInstanceOf('\PPHP\tests\tools\patterns\singleton\TestSingleton', $instance);
    $this->assertEquals(1, $instance->getVar());
    $instance2 = TestSingleton::getInstance();
    $this->assertEquals($instance, $instance2);
    $instance2->setVar(5);
    $this->assertEquals(5, $instance->getVar());
  }

  /**
   * @covers singleton\TSingleton::__clone
   */
  public function test__clone(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\RuntimeException');
    $instance = TestSingleton::getInstance();
    $instance = clone $instance;
  }
}
