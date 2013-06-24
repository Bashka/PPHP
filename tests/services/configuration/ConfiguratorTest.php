<?php
namespace PPHP\tests\services\configuration;

use PPHP\services\configuration\Configurator;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class ConfiguratorTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var Configurator
   */
  private $object;

  protected function setUp(){
    parent::setUp();
    $this->object = Configurator::getInstance();
    $this->object->set('System', 'Test', 'Test');
  }

  protected function tearDown(){
    parent::tearDown();
    $this->object->delete('System', 'Test');
  }

  /**
   * @covers Configurator::__construct
   */
  public function testConstruct(){
    Configurator::getInstance();
  }

  /**
   * @covers Configurator::get
   */
  public function testGet(){
    $this->assertEquals('Test', $this->object->get('System', 'Test'));
    $this->assertEquals('Test', $this->object->System_Test);
  }

  /**
   * @covers Configurator::set
   */
  public function testSet(){
    $this->object->set('System', 'Test', '1');
    $this->assertEquals('1', $this->object->get('System', 'Test'));
    $this->object->System_Test = 2;
    $this->assertEquals('2', $this->object->get('System', 'Test'));
    $this->object->set('System', 'Test', 'Test');
  }

  /**
   * @covers Configurator::isExists
   */
  public function testIsExists(){
    $this->assertTrue($this->object->isExists('System', 'Test'));
    $this->assertTrue(isset($this->object->System_Test));
    $this->assertFalse($this->object->isExists('System', 'x'));
    $this->assertFalse($this->object->isExists('x', 'Version'));
    $this->assertFalse(isset($this->object->System_x));
    $this->assertFalse(isset($this->object->x_Test));
  }

  /**
   * @covers Configurator::delete
   */
  public function testDelete(){
    $this->object->delete('System', 'Test');
    $this->assertFalse($this->object->isExists('System', 'Test'));
    $this->object->set('System', 'Test', 'Test');
    unset($this->object->System_Test);
    $this->assertFalse($this->object->isExists('System', 'Test'));
    $this->object->set('System', 'Test', 'Test');
  }
}
