<?php
namespace PPHP\tests\services\formatting\localisation;

use PPHP\services\formatting\localisation\LocalisationManager;

spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class TestLocaliseClassTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var LocalisationManager
   */
  protected $object;


  /**
   * @var TestLocaliseClass
   */
  protected $localiseClass;


  protected function setUp(){
    $this->object = LocalisationManager::getInstance();
    $this->localiseClass = new TestLocaliseClass();
    $this->object->setLocalise(LocalisationManager::RUSSIA);
  }

  /**
   * @covers LocalisationManager::localiseClass
   */
  public function testLocaliseClass(){
    $this->assertEquals('Тестовый класс', $this->object->localiseClass($this->localiseClass->getReflectionClass()));

    $this->assertEquals('NotLocaliseClassMock', $this->object->localiseClass(NotLocaliseClassMock::getReflectionClass()));
  }

  /**
   * @covers LocalisationManager::localiseProperty
   */
  public function testLocaliseProperty(){
    $this->assertEquals('Сообщение', $this->object->localiseProperty($this->localiseClass->getReflectionClass(), $this->localiseClass->getReflectionProperty('message')));

    $this->assertEquals('message', $this->object->localiseProperty(NotLocaliseClassMock::getReflectionClass(), NotLocaliseClassMock::getReflectionProperty('message')));
  }

  /**
   * @covers LocalisationManager::localiseProperty
   */
  public function testLocaliseMessage(){
    $this->assertEquals('Другие локализованные данные', $this->object->localiseMessage($this->localiseClass->getReflectionClass(), 'OtherData'));
  }
}
