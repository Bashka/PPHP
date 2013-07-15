<?php
namespace PPHP\model\modules\SystemPackages\InstallerModules\tests;

use PPHP\model\modules\SystemPackages\InstallerModules\ReflectionArchiveModule;
use PPHP\model\modules\SystemPackages\ReflectionModule;
use PPHP\services\modules\ModulesRouter;
use PPHP\tools\classes\standard\fileSystem\ComponentFileSystem;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class ReflectionArchiveModuleTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers ReflectionArchiveModule::isDuplication
   */
  public function testIsDuplication(){
    $o = new ReflectionArchiveModule('TestA.zip');
    $this->assertFalse($o->isDuplication());
    $o = new ReflectionArchiveModule('Console.zip');
    $this->assertTrue($o->isDuplication());
  }

  /**
   * @covers ReflectionArchiveModule::addRouter
   */
  public function testAddRouter(){
    $o = new ReflectionArchiveModule('TestA.zip');
    $o->addRouter();
    $router = ModulesRouter::getInstance();
    $this->assertTrue($router->hasModule('TestA'));
    $router->removeModule('TestA');
  }

  /**
   * @covers ReflectionArchiveModule::isParent
   */
  public function testIsParent(){
    $o = new ReflectionArchiveModule('TestA.zip');
    $this->assertTrue($o->isParent());
    $o = new ReflectionArchiveModule('NoParent.zip');
    $this->assertFalse($o->isParent());
  }

  /**
   * @covers ReflectionArchiveModule::sayParent
   */
  public function testSayParent(){
    $o = new ReflectionArchiveModule('TestA.zip');
    $o->sayParent();
    $parent = new ReflectionModule('SystemPackages');
    $this->assertTrue(array_search('TestA', $parent->getChild()) !== false);
    $parent->removeChild('TestA');
  }

  /**
   * @covers ReflectionArchiveModule::isUsed
   */
  public function testIsUsed(){
    $o = new ReflectionArchiveModule('TestA.zip');
    $this->assertTrue($o->isUsed());
    $o = new ReflectionArchiveModule('Used.zip');
    $this->assertTrue($o->isUsed());
    $o = new ReflectionArchiveModule('NoUsed.zip');
    $this->assertEquals('TestA', $o->isUsed());
  }

  /**
   * @covers ReflectionArchiveModule::sayUsed
   */
  public function testSayUsed(){
    $o = new ReflectionArchiveModule('Used.zip');
    $o->sayUsed();
    $used = new ReflectionModule('Console');
    $this->assertTrue(array_search('Used', $used->getDestitute()) !== false);
    $used->removeDestitute('Used');
  }

  /**
   * @covers ReflectionArchiveModule::install
   */
  public function testInstall(){
    $o = new ReflectionArchiveModule('TestA.zip');
    $o->install();
    $this->assertTrue(file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . ModulesRouter::MODULES_DIR . '/SystemPackages/TestA'));
    $this->assertTrue(file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . ModulesRouter::MODULES_DIR . '/SystemPackages/TestA/state.ini'));
    $this->assertTrue(file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . ModulesRouter::MODULES_DIR . '/SystemPackages/TestA/Controller.php'));
    $this->assertTrue(file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . ModulesRouter::MODULES_DIR . '/SystemPackages/TestA/testDir'));
    $this->assertFalse(file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . ModulesRouter::MODULES_DIR . '/SystemPackages/TestA/conf.ini'));
    ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/' . ModulesRouter::MODULES_DIR . '/SystemPackages/TestA')->delete();
  }
}
