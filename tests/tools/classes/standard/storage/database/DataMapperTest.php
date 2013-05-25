<?php
namespace PPHP\tests\tools\classes\standard\storage\database;
use \PPHP\tools\classes\standard\storage\database\DataMapper;
use \PPHP\tests\tools\classes\standard\storage\database\ORM\ParentMock;
use \PPHP\tests\tools\classes\standard\storage\database\ORM\ChildMock;
use PPHP\tools\patterns\database\associations\LongAssociation;

spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class DataMapperTest extends \PHPUnit_Framework_TestCase{

  /**
   * @var PDOMock
   */
  protected $PDO;

  /**
   * @var DataMapper
   */
  protected $object;

  protected function setUp(){
    $this->PDO = new PDOMock('', '', '', '');
    $this->object = new DataMapper;
    $this->object->setPDO($this->PDO);
  }

  /**
   * @covers DataMapper::insert
   */
  public function testInsert(){
    $o = new ParentMock;
    $this->object->insert($o, 5);
    $this->assertEquals('INSERT INTO `ParentTable` (`OID`,ParentTable.af) VALUES ("5","1")', $this->PDO->queries[0]);
    $this->assertEquals(5, $o->getOID());

    $o = new ChildMock;
    $this->object->insert($o, 5);
    $this->assertEquals('INSERT INTO `ParentTable` (`OID`,ParentTable.af) VALUES ("5","1")', $this->PDO->queries[0]);
    $this->assertEquals('INSERT INTO `ChildTable` (`OID`,ChildTable.df,ChildTable.ef,ChildTable.ff,ChildTable.hf) VALUES ("5","4","5","6","")', $this->PDO->queries[1]);
    $this->assertEquals(5, $o->getOID());
  }

  /**
   * @covers DataMapper::update
   */
  public function testUpdate(){
    $o = new ParentMock;
    $o->setOID(5);
    $this->object->update($o);
    $this->assertEquals('UPDATE `ParentTable` SET ParentTable.af = "1" WHERE (`OID` = "5")', $this->PDO->queries[0]);

    $o = new ChildMock;
    $o->setOID(5);
    $this->object->update($o, 5);
    $this->assertEquals('UPDATE `ParentTable` SET ParentTable.af = "1" WHERE (`OID` = "5")', $this->PDO->queries[0]);
    $this->assertEquals('UPDATE `ChildTable` SET ChildTable.df = "4",ChildTable.ef = "5",ChildTable.ff = "6",ChildTable.hf = "" WHERE (`OID` = "5")', $this->PDO->queries[1]);
  }

  /**
   * @covers DataMapper::delete
   */
  public function testDelete(){
    $o = new ParentMock;
    $o->setOID(5);
    $this->object->delete($o);
    $this->assertEquals('DELETE FROM `ParentTable` WHERE (`OID` = "5")', $this->PDO->queries[0]);

    $o = new ChildMock;
    $o->setOID(5);
    $this->object->delete($o, 5);
    $this->assertEquals('DELETE FROM `ParentTable` WHERE (`OID` = "5")', $this->PDO->queries[0]);
    $this->assertEquals('DELETE FROM `ChildTable` WHERE (`OID` = "5")', $this->PDO->queries[1]);
  }

  /**
   * @covers DataMapper::recover
   * @covers DataMapper::setStateObject
   */
  public function testRecover(){
    $o = new ParentMock;
    $o->setOID(5);
    $r = new RowPDOMock;
    $r->rowCount = 1;
    $r->data = [['a' => 10, 'OID' => 1]];
    $this->PDO->restore = $r;
    $this->object->recover($o);
    $this->assertEquals('SELECT ParentTable.af as a,ParentTable.OID as OID FROM `ParentTable`  WHERE (ParentTable.OID = "5")', $this->PDO->queries[0]);
    $this->assertEquals(10, $o->getA());
    $this->assertEquals(5, $o->getOID());

    $this->setUp();
    $o = new ParentMock;
    $o->setOID(5);
    $r = new RowPDOMock;
    $r->rowCount = 1;
    $r->data = [['a' => '$/PPHP/tests/tools/classes/standard/storage/database/ORM/ParentMock:1', 'OID' => 1]];
    $this->PDO->restore = $r;
    $this->object->recover($o);
    $this->assertEquals('SELECT ParentTable.af as a,ParentTable.OID as OID FROM `ParentTable`  WHERE (ParentTable.OID = "5")', $this->PDO->queries[0]);
    $this->assertInstanceOf('\PPHP\tests\tools\classes\standard\storage\database\ORM\ParentMock', $o->getA());
    $this->assertEquals(1, $o->getA()->getOID());

    $this->setUp();
    $o = new ChildMock;
    $o->setOID(5);
    $o2 = new ChildMock;
    $o2->setOID(2);
    $r = new RowPDOMock;
    $r->rowCount = 1;
    $r->data = [['a' => 10, 'd' => $o2, 'e' => 12, 'f' => 13, 'h' => '', 'OID' => 1]];
    $this->PDO->restore = $r;
    $this->object->recover($o);
    $this->assertEquals('SELECT ChildTable.df as d,ChildTable.ef as e,ChildTable.ff as f,ChildTable.hf as h,ParentTable.af as a,ChildTable.OID as OID FROM `ChildTable` INNER JOIN `ParentTable` ON (ChildTable.OID = ParentTable.OID) WHERE (ChildTable.OID = "5")', $this->PDO->queries[0]);
    $this->assertEquals(10, $o->getA());
    $this->assertEquals(2, $o->getD()->getOID());
    $this->assertEquals(12, $o->getE());
    $this->assertEquals(13, $o->f);
    $this->assertInstanceOf('\PPHP\tools\patterns\database\associations\LongAssociation', $o->g);
    $this->assertEquals(0, $o->g->count());
    $this->assertEquals('SELECT ChildTable.df as d,ChildTable.ef as e,ChildTable.ff as f,ChildTable.hf as h,ParentTable.af as a,ChildTable.OID as OID FROM `ChildTable` INNER JOIN `ParentTable` ON (ChildTable.OID = ParentTable.OID) WHERE (ChildTable.hf = "$/PPHP/tests/tools/classes/standard/storage/database/ORM/ChildMock:5")', $o->g->getSelectQuery()->interpretation());
  }

  /**
   * @covers DataMapper::recoverFinding
   */
  public function testRecoverFinding(){
    $o = new ParentMock;
    $r = new RowPDOMock;
    $r->rowCount = 1;
    $r->data = [['a' => 10, 'OID' => 1]];
    $this->PDO->restore = $r;
    $this->object->recoverFinding($o, [['a', '=', '10']]);
    $this->assertEquals('SELECT ParentTable.af as a,ParentTable.OID as OID FROM `ParentTable`  WHERE (ParentTable.af = "10")', $this->PDO->queries[0]);
    $this->assertEquals(10, $o->getA());
    $this->assertEquals(1, $o->getOID());

    $this->setUp();
    $o = new ParentMock;
    $r = new RowPDOMock;
    $r->rowCount = 1;
    $r->data = [['a' => '$/PPHP/tests/tools/classes/standard/storage/database/ORM/ParentMock:1', 'OID' => 2]];
    $this->PDO->restore = $r;
    $this->object->recoverFinding($o, [['a', '=', ParentMock::getProxy(1)]]);
    $this->assertEquals('SELECT ParentTable.af as a,ParentTable.OID as OID FROM `ParentTable`  WHERE (ParentTable.af = "$/PPHP/tests/tools/classes/standard/storage/database/ORM/ParentMock:1")', $this->PDO->queries[0]);
    $this->assertInstanceOf('\PPHP\tests\tools\classes\standard\storage\database\ORM\ParentMock', $o->getA());
    $this->assertEquals(1, $o->getA()->getOID());
    $this->assertEquals(2, $o->getOID());
  }

  /**
   * @covers DataMapper::recoverGroupFinding
   */
  public function testRecoverGroupFinding(){
    $r = new RowPDOMock;
    $r->rowCount = 2;
    $r->data = [['a' => 10, 'OID' => 1], ['a' => 10, 'OID' => 2]];
    $this->PDO->restore = $r;
    $group = $this->object->recoverGroupFinding(ParentMock::getReflectionClass(), [['a', '=', 10]]);

    $this->assertEquals(1, $group[1]->getOID());
    $this->assertEquals(2, $group[2]->getOID());
    $this->assertInstanceOf('\PPHP\tests\tools\classes\standard\storage\database\ORM\ParentMock', $group[1]);
    $this->assertInstanceOf('\PPHP\tests\tools\classes\standard\storage\database\ORM\ParentMock', $group[2]);
    $this->assertEquals(10, $group[1]->getA());
    $this->assertEquals(10, $group[2]->getA());
  }


  /**
   * @covers DataMapper::recoverAssoc
   */
  public function testRecoverAssoc(){
    $o = new ChildMock;
    $o->setOID(1);
    $r = new RowPDOMock;
    $r->rowCount = 1;
    $r->data = [['a' => 10, 'd' => 11, 'e' => 12, 'f' => 13, 'h' => '', 'OID' => 1]];
    $this->PDO->restore = $r;
    $this->object->recover($o);
    $this->assertInstanceOf('\PPHP\tools\patterns\database\associations\LongAssociation', $o->g);
    $this->assertEquals(0, $o->g->count());

    $r = new RowPDOMock;
    $r->rowCount = 2;
    $r->data = [['a' => 10, 'd' => 11, 'e' => 12, 'f' => 13, 'h' => '', 'OID' => 2], ['a' => 10, 'd' => 11, 'e' => 12, 'f' => 13, 'h' => '', 'OID' => 3]];
    $this->PDO->restore = $r;
    $this->object->recoverAssoc($o->g);
    $this->assertEquals(2, $o->g->count());
    $o->g->rewind();
    $object = $o->g->current();
    $this->assertInstanceOf('\PPHP\tests\tools\classes\standard\storage\database\ORM\ParentMock', $object);
    $this->assertEquals(2, $object->getOID());
    $this->assertEquals(10, $object->getA());
  }
}
