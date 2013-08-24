<?php
namespace PPHP\model\modules\SystemPackages\tests;

use PPHP\model\modules\SystemPackages\ReflectionArchiveModule;
use PPHP\model\modules\SystemPackages\ReflectionModule;
use PPHP\tools\classes\standard\fileSystem\ComponentFileSystem;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class ReflectionArchiveModuleTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers ReflectionArchiveModule::__construct
   * @covers ReflectionArchive::__construct
   */
  public function test__construct(){
    new ReflectionArchiveModule('TestA.zip');
    $this->setExpectedException('\PPHP\tools\classes\standard\fileSystem\NotExistsException');
    new ReflectionArchiveModule('x');
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\StructureException');
    new ReflectionArchiveModule('NotController.zip');
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\StructureException');
    new ReflectionArchiveModule('NotName.zip');
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\StructureException');
    new ReflectionArchiveModule('NotConf.zip');
  }

  /**
   * @covers ReflectionArchive::getName
   */
  public function testGetName(){
    $o = new ReflectionArchiveModule('TestA.zip');
    $this->assertEquals('TestA', $o->getName());
  }

  /**
   * @covers ReflectionArchive::getVersion
   */
  public function testGetVersion(){
    $o = new ReflectionArchiveModule('TestA.zip');
    $this->assertEquals('1.0', $o->getVersion());
  }

  /**
   * @covers ReflectionArchiveModule::getType
   */
  public function testGetType(){
    $o = new ReflectionArchiveModule('TestA.zip');
    $this->assertEquals(ReflectionModule::SPECIFIC, $o->getType());
  }

  /**
   * @covers ReflectionArchiveModule::getParent
   * @covers ReflectionArchiveModule::hasParent
   */
  public function testHasGetParent(){
    $o = new ReflectionArchiveModule('TestA.zip');
    $this->assertTrue($o->hasParent());
    $this->assertEquals('SystemPackages', $o->getParent());
  }

  /**
   * @covers ReflectionArchive::getUsed
   * @covers ReflectionArchive::hasUsed
   */
  public function testHasGetUsed(){
    $o = new ReflectionArchiveModule('TestA.zip');
    $this->assertFalse($o->hasUsed());
    $this->assertEquals([], $o->getUsed());
  }

  /**
   * @covers ReflectionArchiveModule::getAccess
   * @covers ReflectionArchiveModule::hasAccess
   */
  public function testHasGetAccess(){
    $o = new ReflectionArchiveModule('TestA.zip');
    $this->assertTrue($o->hasAccess());
    $this->assertEquals('Moderator role', $o->getAccess()['test'][2]);
  }

  /**
   * @covers ReflectionArchiveModule::hasInstaller
   */
  public function testHasInstaller(){
    $o = new ReflectionArchiveModule('TestA.zip');
    $this->assertFalse($o->hasInstaller());
  }

  /**
   * @covers ReflectionArchiveModule::expand
   * @covers ReflectionArchive::expand
   */
  public function testExpand(){
    $o = new ReflectionArchiveModule('TestA.zip');
    $address = $_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/SystemPackages/tests/location';
    $o->expand(ComponentFileSystem::constructDirFromAddress($address));
    $this->assertTrue(file_exists($address . '/Controller.php'));
    $this->assertTrue(file_exists($address . '/state.ini'));
    $this->assertFalse(file_exists($address . '/conf.ini'));
    $this->assertTrue(file_exists($address . '/testDir'));
    ComponentFileSystem::constructDirFromAddress($address)->clear();
  }
}
