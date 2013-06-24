<?php
namespace PPHP\tests\tools\patterns\database\identification;

use PPHP\tests\tools\classes\standard\storage\database\ORM\ParentMock;
use PPHP\tools\patterns\database\identification\TOID;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class OIDTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var TestLongObject
   */
  protected $object;

  protected function setUp(){
    $this->object = new ParentMock;
  }

  /**
   * @covers TOID::setOID
   * @covers TOID::getOID
   */
  public function testSetOID(){
    $this->object->setOID(1);
    $this->assertEquals(1, $this->object->getOID());
    $this->setExpectedException('\PPHP\tools\patterns\database\identification\OIDException');
    $this->object->setOID(1);
  }

  /**
   * @covers TOID::isOID
   */
  public function testIsOID(){
    $this->assertFalse($this->object->isOID());
    $this->object->setOID(1);
    $this->assertTrue($this->object->isOID());
  }

  /**
   * @covers TOID::getProxy
   */
  public function testGetProxy(){
    $object = ParentMock::getProxy(1);
    $this->assertTrue($object->isOID());
    $this->assertEquals(1, $object->getOID());
  }
}
