<?php
namespace PPHP\model\modules\SystemPackages\tests;

use PPHP\model\modules\SystemPackages\ReflectionModule;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class ReflectionModuleTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers ReflectionModule::__construct
   * @covers ReflectionSystemComponent::__construct
   */
  public function test__construct(){
    new ReflectionModule('Console');
    $this->setExpectedException('\PPHP\model\modules\SystemPackages\SystemComponentNotFoundException');
    new ReflectionModule('x');
  }

  /**
   * @covers ReflectionSystemComponent::getName
   */
  public function testGetName(){
    $o = new ReflectionModule('Console');
    $this->assertEquals('Console', $o->getName());
  }

  /**
   * @covers ReflectionModule::getAddress
   */
  public function testGetAddress(){
    $o = new ReflectionModule('Console');
    $this->assertEquals('/PPHP/model/modules/SystemPackages/Console/', $o->getAddress());
  }

  /**
   * @covers ReflectionSystemComponent::getVersion
   */
  public function testGetVersion(){
    $o = new ReflectionModule('Console');
    $this->assertEquals('1.5', $o->getVersion());
  }

  /**
   * @covers ReflectionModule::getType
   */
  public function testGetType(){
    $o = new ReflectionModule('Console');
    $this->assertEquals(ReflectionModule::SPECIFIC, $o->getType());
  }

  /**
   * @covers ReflectionModule::getParent
   */
  public function testGetParent(){
    $o = new ReflectionModule('Console');
    $this->assertEquals('SystemPackages', $o->getParent());
    $o = new ReflectionModule('SystemPackages');
    $this->assertFalse($o->getParent());
  }

  /**
   * @covers ReflectionModule::getChild
   */
  public function testGetChild(){
    $o = new ReflectionModule('SystemPackages');
    $o = $o->getChild();
    $this->assertEquals('Console', $o[0]);
    $this->assertEquals('InstallerModules', $o[1]);
    $this->assertEquals('InstallerScreens', $o[2]);
    $o = new ReflectionModule('Console');
    $this->assertEquals([], $o->getChild());
  }

  /**
   * @covers ReflectionModule::addChild
   * @covers ReflectionModule::removeChild
   */
  public function testAddRemoveChild(){
    $o = new ReflectionModule('Console');
    $this->assertTrue($o->addChild('TestA'));
    $ch = $o->getChild();
    $this->assertEquals('TestA', $ch[0]);
    $this->assertTrue($o->addChild('TestB'));
    $ch = $o->getChild();
    $this->assertEquals('TestB', $ch[1]);
    $this->assertTrue($o->removeChild('TestA'));
    $ch = $o->getChild();
    $this->assertEquals('TestB', $ch[0]);
    $this->assertTrue($o->removeChild('TestB'));
  }

  /**
   * @covers ReflectionModule::getController
   */
  public function testGetController(){
    $o = new ReflectionModule('Console');
    $o = $o->getController();
    $this->assertInstanceOf('\PPHP\model\modules\SystemPackages\Console\Controller', $o);
  }

  /**
   * @covers ReflectionSystemComponent::getDestitute
   */
  public function testGetDestitute(){
    $o = new ReflectionModule('Console');
    $this->assertEquals([], $o->getDestitute());
  }

  /**
   * @covers ReflectionSystemComponent::getDestitute
   * @covers ReflectionSystemComponent::addDestitute
   * @covers ReflectionSystemComponent::removeDestitute
   */
  public function testAddRemoveDestitute(){
    $o = new ReflectionModule('Console');
    $this->assertTrue($o->addDestitute('TestA'));
    $ch = $o->getDestitute();
    $this->assertEquals('TestA', $ch[0]);
    $this->assertTrue($o->addDestitute('TestB'));
    $ch = $o->getDestitute();
    $this->assertEquals('TestB', $ch[1]);
    $this->assertTrue($o->removeDestitute('TestA'));
    $ch = $o->getDestitute();
    $this->assertEquals('TestB', $ch[0]);
    $this->assertTrue($o->removeDestitute('TestB'));
  }

  /**
   * @covers ReflectionSystemComponent::getUsed
   */
  public function testGetUsed(){
    $o = new ReflectionModule('Console');
    $this->assertEquals([], $o->getUsed());
  }

  /**
   * @covers ReflectionModule::getAccess
   */
  public function testGetAccess(){
    $o = new ReflectionModule('Console');
    $o = $o->getAccess();
    $this->assertEquals('Default user role', $o['synchCahce'][0]);
    $this->assertEquals('Moderator role', $o['removeAllFiles'][2]);
  }
}
