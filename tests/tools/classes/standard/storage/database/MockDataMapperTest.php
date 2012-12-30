<?php
namespace PPHP\tests\tools\classes\standard\storage\database;
$_SERVER['DOCUMENT_ROOT'] = 'C:/WebServers/home/dic/www';
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-09-08 at 08:11:00.
 */
class MockDataMapperTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var MockDataMapper
   */
  protected $object;

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp(){
    $this->object = new MockDataMapper;
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown(){
  }

  /**
   * @covers PPHP\tests\tools\classes\standard\storage\database\MockDataMapper::insert
   */
  public function testInsert(){
    $obj = new \PPHP\tests\tools\patterns\database\TestLongObject;
    $this->object->setReturns([2]);
    $this->object->insert($obj);
    $this->assertEquals('insert(PPHP\tests\tools\patterns\database\TestLongObject[privProp=1,protProp=2,publProp=,linkProp=,parentProtProp=2,parentPublProp=3,OID=])', $this->object->getCommand(0));
    $this->assertEquals(2, $obj->getOID());
  }

  /**
   * @covers PPHP\tests\tools\classes\standard\storage\database\MockDataMapper::delete
   */
  public function testDelete(){
    $obj = new \PPHP\tests\tools\patterns\database\TestLongObject;
    $obj->setOID(1);
    $this->object->delete($obj);
    $this->assertEquals('delete(PPHP\tests\tools\patterns\database\TestLongObject[privProp=1,protProp=2,publProp=,linkProp=,parentProtProp=2,parentPublProp=3,OID=1])', $this->object->getCommand(0));
  }

  /**
   * @covers PPHP\tests\tools\classes\standard\storage\database\MockDataMapper::recover
   */
  public function testRecover(){
    $obj = new \PPHP\tests\tools\patterns\database\TestLongObject;
    $obj->setOID(1);
    $this->object->setReturns([['privProp' => 2, 'protProp' => 3, 'publProp' => 4, 'OID' => 1], ['privProp' => 3, 'protProp' => 4, 'publProp' => 5, 'OID' => 1]]);
    $this->object->recover($obj);
    $this->assertEquals('recover(PPHP\tests\tools\patterns\database\TestLongObject[privProp=1,protProp=2,publProp=,linkProp=,parentProtProp=2,parentPublProp=3,OID=1])', $this->object->getCommand(0));
    $this->assertEquals(2, $obj->getPrivProp());
    $this->assertEquals(3, $obj->getProtProp());
    $this->assertEquals(4, $obj->getPublProp());
    $this->assertEquals(1, $obj->getOID());

    $this->object->recover($obj);
    $this->assertEquals('recover(PPHP\tests\tools\patterns\database\TestLongObject[privProp=2,protProp=3,publProp=4,linkProp=,parentProtProp=2,parentPublProp=3,OID=1])', $this->object->getCommand(1));
    $this->assertEquals(3, $obj->getPrivProp());
    $this->assertEquals(4, $obj->getProtProp());
    $this->assertEquals(5, $obj->getPublProp());
    $this->assertEquals(1, $obj->getOID());
  }

  /**
   * @covers PPHP\tests\tools\classes\standard\storage\database\MockDataMapper::recoverFinding
   */
  public function testRecoverFinding(){
    $obj = new \PPHP\tests\tools\patterns\database\TestLongObject;
    $obj->setOID(1);
    $this->object->setReturns([['privProp' => 2, 'protProp' => 3, 'publProp' => 4, 'OID' => 1], 1, ['privProp' => 3, 'protProp' => 4, 'publProp' => 5, 'OID' => 1], 2, [], new \PPHP\tools\classes\standard\baseType\exceptions\Exception]);
    $this->assertEquals(1, $this->object->recoverFinding($obj, ['publProp' => 1]));
    $this->assertEquals('recoverFinding(PPHP\tests\tools\patterns\database\TestLongObject[privProp=1,protProp=2,publProp=,linkProp=,parentProtProp=2,parentPublProp=3,OID=1],[publProp=1])', $this->object->getCommand(0));
    $this->assertEquals(2, $obj->getPrivProp());
    $this->assertEquals(3, $obj->getProtProp());
    $this->assertEquals(4, $obj->getPublProp());
    $this->assertEquals(1, $obj->getOID());

    $this->assertEquals(2,$this->object->recoverFinding($obj, ['publProp' => 2]));
    $this->assertEquals('recoverFinding(PPHP\tests\tools\patterns\database\TestLongObject[privProp=2,protProp=3,publProp=4,linkProp=,parentProtProp=2,parentPublProp=3,OID=1],[publProp=2])', $this->object->getCommand(1));
    $this->assertEquals(3, $obj->getPrivProp());
    $this->assertEquals(4, $obj->getProtProp());
    $this->assertEquals(5, $obj->getPublProp());
    $this->assertEquals(1, $obj->getOID());

    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\Exception');
    $this->object->recoverFinding($obj, ['publProp' => 3]);
  }

  /**
   * @covers PPHP\tests\tools\classes\standard\storage\database\MockDataMapper::recoverGroupFinding
   */
  public function testRecoverGroupFinding(){
    $obj = new \PPHP\tests\tools\patterns\database\TestLongObject;
    $res1 = [1,2,3];
    $res2 = [2,3,4];
    $this->object->setReturns([$res1, $res2]);
    $res = $this->object->recoverGroupFinding($obj::getReflectionClass(), ['publProp' => 1]);
    $this->assertEquals('recoverGroupFinding(PPHP\tests\tools\patterns\database\TestLongObject,[publProp=1])', $this->object->getCommand(0));
    $this->assertEquals($res1, $res);

    $res = $this->object->recoverGroupFinding($obj::getReflectionClass(), ['publProp' => 2]);
    $this->assertEquals('recoverGroupFinding(PPHP\tests\tools\patterns\database\TestLongObject,[publProp=2])', $this->object->getCommand(1));
    $this->assertEquals($res2, $res);
  }

  /**
   * @covers PPHP\tests\tools\classes\standard\storage\database\MockDataMapper::recoverAssoc
   */
  public function testRecoverAssoc(){
    $select = new \PPHP\tools\patterns\database\query\Select();
    $select->addTable(new \PPHP\tools\patterns\database\query\Table('Table'));
    $select->addField(new \PPHP\tools\patterns\database\query\Field('Field'));
    $assoc = new \PPHP\tools\patterns\database\associations\LongAssociation($select, \PPHP\tests\tools\patterns\database\TestLongObject::getReflectionClass());

    $resObj = new \stdClass();
    $resObj->x = 1;
    $this->object->setReturns([[$resObj]]);

    $this->object->recoverAssoc($assoc);
    $this->assertEquals('recoverAssoc(PPHP\tests\tools\patterns\database\TestLongObject,SELECT `Field` FROM `Table`)', $this->object->getCommand(0));
    $assoc->rewind();
    $this->assertEquals($resObj, $assoc->current());
  }

  /**
   * @covers PPHP\tests\tools\classes\standard\storage\database\MockDataMapper::update
   */
  public function testUpdate(){
    $obj = new \PPHP\tests\tools\patterns\database\TestLongObject;
    $obj->setOID(1);
    $this->object->update($obj);
    $this->assertEquals('update(PPHP\tests\tools\patterns\database\TestLongObject[privProp=1,protProp=2,publProp=,linkProp=,parentProtProp=2,parentPublProp=3,OID=1])', $this->object->getCommand(0));
  }
}
