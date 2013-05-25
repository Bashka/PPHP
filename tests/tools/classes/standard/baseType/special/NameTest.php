<?php
namespace PPHP\tests\tools\classes\standard\baseType\special;
use PPHP\tools\classes\standard\baseType\special\Name;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class NameTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers Name::isReestablish
   */
  public function testIsReestablish(){
    $this->assertFalse(Name::isReestablish(''));
    $this->assertTrue(Name::isReestablish('a'));
    $this->assertTrue(Name::isReestablish('_a'));
    $this->assertTrue(Name::isReestablish('a_b'));
    $this->assertTrue(Name::isReestablish('a1'));
    $this->assertTrue(Name::isReestablish('_1'));

    $this->assertFalse(Name::isReestablish('1a'));
  }
}
