<?php
namespace PPHP\tests\tools\classes\standard\baseType\special;

use PPHP\tools\classes\standard\baseType\special\SessionID;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class SessionIDTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers SessionID::isReestablish
   */
  public function testIsReestablish(){
    $this->assertTrue(SessionID::isReestablish('a'));
    $this->assertTrue(SessionID::isReestablish('A'));
    $this->assertTrue(SessionID::isReestablish('1'));
    $this->assertTrue(SessionID::isReestablish('a-b'));
    $this->assertTrue(SessionID::isReestablish('a12-B34'));
    $this->assertTrue(SessionID::isReestablish('-'));
    $this->assertFalse(SessionID::isReestablish(''));
    $this->assertFalse(SessionID::isReestablish('_'));
    $this->assertFalse(SessionID::isReestablish('*'));
  }
}
