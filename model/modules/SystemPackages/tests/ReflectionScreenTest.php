<?php
namespace PPHP\model\modules\SystemPackages\tests;

use PPHP\model\modules\SystemPackages\ReflectionScreen;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class ReflectionScreenTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers ReflectionScreen::getModuleName
   */
  public function testGetModuleName(){
    $this->assertEquals('Console', (new ReflectionScreen('Console:browse'))->getModuleName());
  }

  /**
   * @covers ReflectionScreen::getScreenName
   */
  public function testGetScreenName(){
    $this->assertEquals('browse', (new ReflectionScreen('Console:browse'))->getScreenName());
  }

  /**
   * @covers ReflectionScreen::getAddress
   */
  public function testGetAddress(){
    $this->assertEquals('/PPHP/view/screens/Console/browse/', (new ReflectionScreen('Console:browse'))->getAddress());
  }
}
