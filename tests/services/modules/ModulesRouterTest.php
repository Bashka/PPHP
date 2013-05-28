<?php
namespace PPHP\tests\services\modules;

use PPHP\services\modules\ModulesRouter;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class ModulesRouterTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var ModulesRouter
   */
  protected $object;

  protected function setUp(){
    parent::setUp();
    $this->object = ModulesRouter::getInstance();
  }

  /**
   * @covers ModulesRouter::getModule
   */
  public function testGetModule(){
    $this->assertEquals('Console', $this->object->getModule('Console'));
    $this->assertEquals('SystemPackages/InstallerModules', $this->object->getModule('InstallerModules'));
  }

  /**
   * @covers ModulesRouter::getReflectionModule
   */
  public function testGetReflectionModule(){
    $rm = $this->object->getReflectionModule('Console');
    $this->assertInstanceOf('PPHP\tools\patterns\metadata\reflection\ReflectionModule', $rm);
  }

  /**
   * @covers ModulesRouter::getController
   */
  public function testGetController(){
    $c = $this->object->getReflectionModule('Console')->getController();
    $this->assertInstanceOf('\PPHP\model\classes\ModuleController', $c);
    $this->assertEquals('test', $c->getReflectionMethod('test')->getName());
  }

  /**
   * @covers ModulesRouter::hasModule
   */
  public function testHasModule(){
    $this->assertTrue($this->object->hasModule('Console'));
    $this->assertFalse($this->object->hasModule('X'));
  }

  /**
   * @covers ModulesRouter::addModule
   * @covers ModulesRouter::removeModule
   */
  public function testAddModuleRemoveModule(){
    $this->object->addModule('X', 'SystemPackages');
    $this->assertTrue($this->object->hasModule('X'));
    $this->object->removeModule('X', 'SystemPackages');
    $this->assertFalse($this->object->hasModule('X'));

    $this->setExpectedException('\PPHP\services\modules\ModuleDuplicationException');
    $this->object->addModule('Console');
  }
}
