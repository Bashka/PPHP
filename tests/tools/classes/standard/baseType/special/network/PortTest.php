<?php
namespace PPHP\tests\tools\classes\standard\baseType\special\network;
use PPHP\tools\classes\standard\baseType\special\network\Port;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class PortTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers Port::isReestablish
   */
  public function testIsReestablish(){
    $this->assertFalse(Port::isReestablish(''));
    $this->assertTrue(Port::isReestablish('0'));
    $this->assertTrue(Port::isReestablish('80'));
    $this->assertTrue(Port::isReestablish('65536'));
    $this->assertFalse(Port::isReestablish('-1'));
    $this->assertFalse(Port::isReestablish('65537'));
    $this->assertFalse(Port::isReestablish('a'));
  }

  /**
   * @covers Port::reestablish
   */
  public function testReestablish(){
    $o = Port::reestablish('80');
    $this->assertEquals(80, $o->getVal());
  }
}
