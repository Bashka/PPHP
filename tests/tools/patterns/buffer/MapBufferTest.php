<?php
namespace PPHP\tests\tools\patterns\buffer;
use \PPHP\tests\tools\patterns\buffer\TestBuffer;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class MapBufferTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var TestBuffer
   */
  protected $object;

  protected function setUp(){
    $this->object = new TestBuffer();
  }

  /**
   * @covers MapBuffer::getData
   */
  public function testGetDate(){
    $this->assertEquals('testKey', $this->object->get('testKey'));
  }

  /**
   * @covers MapBuffer::__construct
   */
  public function test_construct(){
    $this->assertEquals(50, $this->object->getMaxSizeBuffer());
  }

  /**
   * @covers MapBuffer::setMaxSizeBuffer
   * @covers MapBuffer::getMaxSizeBuffer
   */
  public function testSetMaxSizeBuffer(){
    $this->object->setMaxSizeBuffer(20);
    $this->assertEquals(20, $this->object->getMaxSizeBuffer());
  }

  /**
   * @covers MapBuffer::getSizeBuffer
   */
  public function testGetSizeBufferForEmptyBuffer(){
    $this->assertEquals(0, $this->object->getSizeBuffer());
  }

  /**
   * @covers MapBuffer::getSizeBuffer
   */
  public function testGetSizeBufferForNonEmptyBuffer(){
    $this->object->get('testKey');
    $this->assertEquals(1, $this->object->getSizeBuffer());
  }

  /**
   * @covers MapBuffer::getData
   */
  public function testGetDateForFullBuffer(){
    $this->object->setMaxSizeBuffer(2);
    $this->object->get('1');
    $this->object->get('2');
    $this->object->get('3');
    $this->assertEquals(2, $this->object->getSizeBuffer());
  }
}
