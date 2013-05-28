<?php
namespace PPHP\tests\services\log;
use PPHP\services\log\LogManager;
use PPHP\services\log\Message;

spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class LogManagerTest extends \PHPUnit_Framework_TestCase {
  /**
   * @var LogManager
   */
  protected $object;

  protected function setUp(){
    $this->object = LogManager::getInstance();
  }

  /**
   * @covers LogManager::setType
   * @covers LogManager::getType
   */
  public function testSetTypeGetType(){
    $this->object->setType(LogManager::ERROR);
    $this->assertEquals(LogManager::ERROR, $this->object->getType());
  }

  /**
   * @covers LogManager::setMessage
   */
  public function testSetMessage(){
    $this->object->setType(LogManager::ERROR);
    $e = Message::createError('Test');
    $this->object->setMessage($e);
    $this->assertEquals('ERROR['.$e->getDate().']: Test;'."\n", file_get_contents($_SERVER['DOCUMENT_ROOT'].'/PPHP/services/log/log.txt'));
    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/PPHP/services/log/log.txt', '');

    $this->object->setMessage(Message::createInfo('Test'));
    $this->assertEquals('', file_get_contents($_SERVER['DOCUMENT_ROOT'].'/PPHP/services/log/log.txt'));
  }
}
