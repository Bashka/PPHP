<?php
namespace PPHP\tests\tools\classes\standard\baseType\special\network;

use PPHP\tools\classes\standard\baseType\special\network\DomainName;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class DomainNameTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers DomainName::isReestablish
   */
  public function testIsReestablish(){
    $this->assertFalse(DomainName::isReestablish(''));
    $this->assertFalse(DomainName::isReestablish('a'));
    $this->assertFalse(DomainName::isReestablish('1'));
    $this->assertTrue(DomainName::isReestablish('ab'));
    $this->assertTrue(DomainName::isReestablish('1a'));
    $this->assertTrue(DomainName::isReestablish('a1'));
    $this->assertTrue(DomainName::isReestablish('11'));
    $this->assertTrue(DomainName::isReestablish('a-b'));
    $this->assertFalse(DomainName::isReestablish('a--'));
    $this->assertTrue(DomainName::isReestablish('a.b'));
    $this->assertFalse(DomainName::isReestablish('a.'));
    $this->assertFalse(DomainName::isReestablish('.a'));
    $this->assertTrue(DomainName::isReestablish('a.1'));
    $this->assertTrue(DomainName::isReestablish('a-.b'));
    $this->assertTrue(DomainName::isReestablish('test.com'));
    $this->assertTrue(DomainName::isReestablish('sub.domain.com'));
    $this->assertFalse(DomainName::isReestablish('-a.1'));
    $this->assertFalse(DomainName::isReestablish('a_.b'));
  }

  /**
   * @covers DomainName::reestablish
   * @covers DomainName::getComponent
   */
  public function testReestablish(){
    $o = DomainName::reestablish('test.domain1.na-me.r-u');
    $this->assertEquals('r-u', $o->getComponent(0));
    $this->assertEquals('na-me', $o->getComponent(1));
    $this->assertEquals('domain1', $o->getComponent(2));
    $this->assertEquals('test', $o->getComponent(3));
  }
}
