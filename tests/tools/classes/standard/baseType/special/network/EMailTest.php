<?php
namespace PPHP\tests\tools\classes\standard\baseType\special\network;
use PPHP\tools\classes\standard\baseType\special\network\EMail;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class EMailTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers EMail::isReestablish
   */
  public function testIsReestablish(){
    $this->assertFalse(EMail::isReestablish(''));
    $this->assertTrue(EMail::isReestablish('a@b.c'));
    $this->assertTrue(EMail::isReestablish('1_-a@email.com'));
    $this->assertTrue(EMail::isReestablish('Login@email.com'));
    $this->assertFalse(EMail::isReestablish('#@email.com'));
    $this->assertFalse(EMail::isReestablish('Loginemail.com'));
    $this->assertFalse(EMail::isReestablish('Login@'));
    $this->assertFalse(EMail::isReestablish('@email.com'));
    $this->assertFalse(EMail::isReestablish('Login@1'));
  }

  /**
   * @covers EMail::reestablish
   * @covers EMail::getDomain
   * @covers EMail::getLocal
   */
  public function testReestablish(){
    $o = EMail::reestablish('Login@email.com');
    $this->assertEquals('Login', $o->getLocal());
    $this->assertEquals('com', $o->getDomain()->getComponent(0));
  }
}
