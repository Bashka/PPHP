<?php
namespace PPHP\tests\tools\classes\standard\baseType\special\people;

use PPHP\tools\classes\standard\baseType\special\people\PhoneNumber;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class PhoneNumberTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers PhoneNumber::isReestablish
   */
  public function testIsReestablish(){
    $this->assertTrue(PhoneNumber::isReestablish('+9(99999)999999999'));
    $this->assertTrue(PhoneNumber::isReestablish('+123(1)1'));
    $this->assertFalse(PhoneNumber::isReestablish(''));
    $this->assertFalse(PhoneNumber::isReestablish('+0(0)0'));
    $this->assertFalse(PhoneNumber::isReestablish('0(123)4567890'));
    $this->assertFalse(PhoneNumber::isReestablish('+0123456789'));
    $this->assertFalse(PhoneNumber::isReestablish('+0(123)'));
    $this->assertFalse(PhoneNumber::isReestablish('(123)456789'));
    $this->assertFalse(PhoneNumber::isReestablish('123'));
    $this->assertFalse(PhoneNumber::isReestablish('abc'));
  }

  /**
   * @covers PhoneNumber::reestablish
   * @covers PhoneNumber::getCode
   * @covers PhoneNumber::getNumber
   * @covers PhoneNumber::getRegion
   */
  public function testReestablish(){
    $o = PhoneNumber::reestablish('+1(234)5678901');
    $this->assertEquals('1', $o->getRegion());
    $this->assertEquals('234', $o->getCode());
    $this->assertEquals('5678901', $o->getNumber());
  }
}
