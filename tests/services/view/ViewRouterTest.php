<?php
namespace PPHP\tests\services\view;

use PPHP\services\view\ViewRouter;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class ViewRouterTest extends \PHPUnit_Framework_TestCase {
  /**
   * @var ViewRouter
   */
  protected $object;

  protected function setUp(){
    parent::setUp();
    $this->object = ViewRouter::getInstance();
  }

  /**
   * @covers ViewRouter::getScreen
   */
  public function testGetScreen(){
    $this->assertEquals('Console/browse', $this->object->getScreen('Console', 'browse'));
  }

  /**
   * @covers ViewRouter::hasScreen
   */
  public function testHasScreen(){
    $this->assertTrue($this->object->hasScreen('Console', 'browse'));
    $this->assertFalse($this->object->hasScreen('Console', 'x'));
    $this->assertFalse($this->object->hasScreen('x', 'browse'));
  }

  /**
   * @covers ViewRouter::addScreen
   * @covers ViewRouter::removeScreen
   */
  public function testAddScreenRemoveScreen(){
    $this->object->addScreen('Console', 'x', 'Console/x');
    $this->assertTrue($this->object->hasScreen('Console', 'x'));
    $this->object->removeScreen('Console', 'x');
    $this->assertFalse($this->object->hasScreen('Console', 'x'));

    $this->setExpectedException('\PPHP\services\view\ScreenDuplicationException');
    $this->object->addScreen('Console', 'browse', 'Console/browse');
  }
}
