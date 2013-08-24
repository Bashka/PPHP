<?php
namespace PPHP\tests\services\database;

use PPHP\services\database\ConnectionManager;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class ConnectionManagerTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var ConnectionManager
   */
  protected $object;

  protected function setUp(){
    $this->object = ConnectionManager::getInstance();
  }

  /**
   * @covers ConnectionManager::getNewPDO
   */
  public function testGetNewPDO(){
    $PDO = $this->object->getNewPDO();
    $this->assertFalse($PDO === $this->object->getNewPDO());
    $this->assertInstanceOf('\PPHP\tools\classes\standard\storage\database\PDO', $PDO);
  }

  /**
   * @covers ConnectionManager::getPDO
   */
  public function testGetPDO(){
    $this->assertInstanceOf('\PPHP\tools\classes\standard\storage\database\PDO', $this->object->getNewPDO());
  }

  /**
   * @covers ConnectionManager::getInstance
   */
  public function testGetInstance(){
    $connectManager = ConnectionManager::getInstance();
    $this->assertInstanceOf('\PPHP\services\database\ConnectionManager', $connectManager);
    $this->assertTrue($connectManager === $this->object);
  }

  /**
   * @covers ConnectionManager::setAttribute
   * @covers ConnectionManager::getAttribute
   */
  public function testSetAttribute(){
    $connectManager = ConnectionManager::getInstance();
    $rewriteAttributeValue = $this->object->getAttribute('User');
    $connectManager->setAttribute('User', 'Test');
    $this->assertEquals('Test', $this->object->getAttribute('User'));
    $connectManager->setAttribute('User', $rewriteAttributeValue);
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    $connectManager->setAttribute('X', 'Test');
  }
}
