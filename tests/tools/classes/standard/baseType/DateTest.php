<?php
namespace PPHP\tests\tools\classes\standard\baseType;

use PPHP\tools\classes\standard\baseType\Date;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class DateTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers Date::__construct
   */
  public function test__construct(){
    new Date(new \DateTime());
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new Date(1);
  }

  /**
   * @covers Date::reestablish
   */
  public function testReestablish(){
    $o = Date::reestablish('1.1.1970');
    $this->assertEquals('01.01.1970', (string) $o);
  }

  /**
   * @covers Date::isReestablish
   */
  public function testisReestablish(){
    $this->assertTrue(Date::isReestablish('1.1.1970'));
    $this->assertFalse(Date::isReestablish(''));
    $this->assertFalse(Date::isReestablish('t'));
    $this->assertFalse(Date::isReestablish('1'));
    $this->assertFalse(Date::isReestablish('0.1.1970'));
    $this->assertFalse(Date::isReestablish('1.13.1970'));
    $this->assertFalse(Date::isReestablish('32.1.1970'));
  }

  /**
   * @covers Date::__toString
   */
  public function test__toString(){
    $this->assertEquals('01.01.1970', (string) Date::reestablish('1.1.1970'));
  }
}
