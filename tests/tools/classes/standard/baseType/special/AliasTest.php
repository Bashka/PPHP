<?php
namespace PPHP\tests\tools\classes\standard\baseType\special;

use PPHP\tools\classes\standard\baseType\special\Alias;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class AliasTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers Alias::isReestablish
   */
  public function testIsReestablish(){
    $this->assertTrue(Alias::isReestablish('a1 b'));
    $this->assertFalse(Alias::isReestablish(''));
    $this->assertFalse(Alias::isReestablish('_-?*-/!@#'));
  }
}
