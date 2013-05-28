<?php
namespace PPHP\tests\services\database;

use PPHP\services\database\DataMapperManager;

spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class DataMapperManagerTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var DataMapperManager
   */
  protected $object;

  protected function setUp(){
    $this->object = DataMapperManager::getInstance();
  }

  /**
   * @covers DataMapperManager::getInstance
   */
  public function testGetInstance(){
    $object = DataMapperManager::getInstance();
    $this->assertInstanceOf('\PPHP\services\database\DataMapperManager', $object);
    $this->assertTrue($object === $this->object);
  }

  /**
   * @covers DataMapperManager::getNewDataMapper
   */
  public function testGetNewDataMapper(){
    $mapper = $this->object->getNewDataMapper();
    $this->assertFalse($mapper === $this->object->getNewDataMapper());
    $this->assertInstanceOf('\PPHP\tools\classes\standard\storage\database\DataMapper', $mapper);
  }

  /**
   * @covers DataMapperManager::getDataMapper
   */
  public function testGetDataMapper(){
    $this->assertInstanceOf('\PPHP\tools\classes\standard\storage\database\DataMapper', $this->object->getDataMapper());
  }
}
