<?php
namespace PPHP\tests\tools\classes\standard\baseType\special\network;

use PPHP\tools\classes\standard\baseType\special\network\IPAddress4;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class IPAddress4Test extends \PHPUnit_Framework_TestCase{
  /**
   * @covers IPAddress4::isReestablish
   */
  public function testIsReestablish(){
    $this->assertFalse(IPAddress4::isReestablish(''));
    $this->assertTrue(IPAddress4::isReestablish('0.0.0.0'));
    $this->assertTrue(IPAddress4::isReestablish('127.0.0.1'));
    $this->assertTrue(IPAddress4::isReestablish('255.255.255.255'));
    $this->assertFalse(IPAddress4::isReestablish('-1.0.0.0'));
    $this->assertFalse(IPAddress4::isReestablish('256.0.0.0'));
    $this->assertFalse(IPAddress4::isReestablish('0.0.0.'));
    $this->assertFalse(IPAddress4::isReestablish('0.0.0'));
    $this->assertFalse(IPAddress4::isReestablish('0.0.0.0.'));
    $this->assertFalse(IPAddress4::isReestablish('0.0.0.0.0'));
  }

  /**
   * @covers IPAddress4::reestablish
   * @covers IPAddress4::getTrio
   * @covers IPAddress4::getTrioBin
   */
  public function testReestablish(){
    $o = IPAddress4::reestablish('127.0.0.1');
    $this->assertEquals(127, $o->getTrio(0));
    $this->assertEquals(0, $o->getTrio(1));
    $this->assertEquals(0, $o->getTrio(2));
    $this->assertEquals(1, $o->getTrio(3));
    $this->assertEquals('1111111', $o->getTrioBin(0));
  }
}
