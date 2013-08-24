<?php
namespace PPHP\tests\tools\classes\standard\storage\database;

use PPHP\tests\tools\classes\standard\storage\database\ORM\ChildMock;
use PPHP\tests\tools\classes\standard\storage\database\ORM\ParentMock;
use PPHP\tools\classes\standard\storage\database\DataMapper;

spl_autoload_register(function ($className){
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
   * @covers DataMapper::isDuplicate
   */
  public function testIsDuplicate(){
    $o = ChildMock::getProxy(1);
    $this->PDO->restore[] = new RowPDOMock([]);
    $this->assertFalse($this->object->isDuplicate($o));
    $this->assertEquals('SELECT ChildTable.df AS d,ChildTable.ef AS e,ChildTable.ff AS f,ChildTable.hf AS h,ParentTable.af AS a,ChildTable.OID AS OID FROM `ChildTable` INNER JOIN `ParentTable` ON (ChildTable.OID = ParentTable.OID) WHERE ((ChildTable.df = "4") AND (ChildTable.ef = "5"))', $this->PDO->queries[0]);
    $this->setUp();
    $o = ChildMock::getProxy(1);
    $this->PDO->restore[] = new RowPDOMock(['d' => 4, 'e' => 5]);
    $this->assertTrue($this->object->isDuplicate($o));
  }

  /**
   * @covers DataMapper::insert
   */
  public function testInsert(){
    $o = new ParentMock;
    $this->object->insert($o, 5);
    $this->assertEquals('INSERT INTO `ParentTable` (`OID`,ParentTable.af) VALUES ("5","1")', $this->PDO->queries[0]);
    $this->assertEquals(5, $o->getOID());
    $this->setUp();
    $o = new ChildMock;
    $this->PDO->restore[] = new RowPDOMock([]); // Ответ для механизма контроля дублирования
    $this->object->insert($o, 5);
    $this->assertEquals('INSERT INTO `ParentTable` (`OID`,ParentTable.af) VALUES ("5","1")', $this->PDO->queries[2]); // Запрос с индексом 0 отвечает за контроль дублирования
    $this->assertEquals('INSERT INTO `ChildTable` (`OID`,ChildTable.df,ChildTable.ef,ChildTable.ff,ChildTable.hf) VALUES ("5","4","5","6","")', $this->PDO->queries[1]);
    $this->assertEquals(5, $o->getOID());
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $this->setUp();
    $this->PDO->restore[] = new RowPDOMock(['d' => 4, 'e' => 5]); // Ответ для механизма контроля дублирования
    $this->object->insert($o, 5);
  }

  /**
   * @covers DataMapper::update
   */
  public function testUpdate(){
    $o = new ParentMock;
    $this->PDO->restore[] = new RowPDOMock([]);
    $o->setOID(5);
    $this->object->update($o);
    $this->assertEquals('UPDATE `ParentTable` SET ParentTable.af = "1" WHERE (`OID` = "5")', $this->PDO->queries[0]);
    $this->setUp();
    $o = new ChildMock;
    $this->PDO->restore[] = new RowPDOMock([]); // Ответ для механизма контроля дублирования
    $o->setOID(5);
    $this->object->update($o, 5);
    $this->assertEquals('UPDATE `ParentTable` SET ParentTable.af = "1" WHERE (`OID` = "5")', $this->PDO->queries[2]); // Запрос с индексом 0 отвечает за контроль дублирования
    $this->assertEquals('UPDATE `ChildTable` SET ChildTable.df = "4",ChildTable.ef = "5",ChildTable.ff = "6",ChildTable.hf = "" WHERE (`OID` = "5")', $this->PDO->queries[1]);
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $this->setUp();
    $o = new ChildMock;
    $this->PDO->restore[] = new RowPDOMock(['d' => 4, 'e' => 5]); // Ответ для механизма контроля дублирования
    $o->setOID(5);
    $this->object->update($o, 5);
  }

  /**
   * @covers DataMapper::delete
   */
  public function testDelete(){
    $o = new ParentMock;
    $o->setOID(5);
    $this->object->delete($o);
    $this->assertEquals('DELETE FROM `ParentTable` WHERE (`OID` = "5")', $this->PDO->queries[0]);
    $this->setUp();
    $o = ChildMock::getProxy(1);
    $this->PDO->restore[] = new RowPDOMock([['a' => 10, 'h' => '$/PPHP/tests/tools/classes/standard/storage/database/ORM/ChildMock:1', 'OID' => 2], ['a' => 10, 'h' => '$/PPHP/tests/tools/classes/standard/storage/database/ORM/ChildMock:1', 'OID' => 3]]);
    $this->PDO->restore[] = new RowPDOMock([]); // Восстановление ассоциации g композитов объекта 2
    $this->PDO->restore[] = new RowPDOMock([]); // Delete объекта 2
    $this->PDO->restore[] = new RowPDOMock([]); // Delete объекта 2
    $this->PDO->restore[] = new RowPDOMock([]); // Восстановление ассоциации g композитов объекта 3
    $this->object->delete($o);
    $this->assertEquals('SELECT ChildTable.df AS d,ChildTable.ef AS e,ChildTable.ff AS f,ChildTable.hf AS h,ParentTable.af AS a,ChildTable.OID AS OID FROM `ChildTable` INNER JOIN `ParentTable` ON (ChildTable.OID = ParentTable.OID) WHERE (ChildTable.hf = "$/PPHP/tests/tools/classes/standard/storage/database/ORM/ChildMock:1")', $this->PDO->queries[0]);
    $this->assertEquals('SELECT ChildTable.df AS d,ChildTable.ef AS e,ChildTable.ff AS f,ChildTable.hf AS h,ParentTable.af AS a,ChildTable.OID AS OID FROM `ChildTable` INNER JOIN `ParentTable` ON (ChildTable.OID = ParentTable.OID) WHERE (ChildTable.hf = "$/PPHP/tests/tools/classes/standard/storage/database/ORM/ChildMock:2")', $this->PDO->queries[1]);
    $this->assertEquals('DELETE FROM `ChildTable` WHERE (`OID` = "2")', $this->PDO->queries[2]);
    $this->assertEquals('DELETE FROM `ParentTable` WHERE (`OID` = "2")', $this->PDO->queries[3]);
    $this->assertEquals('SELECT ChildTable.df AS d,ChildTable.ef AS e,ChildTable.ff AS f,ChildTable.hf AS h,ParentTable.af AS a,ChildTable.OID AS OID FROM `ChildTable` INNER JOIN `ParentTable` ON (ChildTable.OID = ParentTable.OID) WHERE (ChildTable.hf = "$/PPHP/tests/tools/classes/standard/storage/database/ORM/ChildMock:3")', $this->PDO->queries[4]);
    $this->assertEquals('DELETE FROM `ChildTable` WHERE (`OID` = "3")', $this->PDO->queries[5]);
    $this->assertEquals('DELETE FROM `ParentTable` WHERE (`OID` = "3")', $this->PDO->queries[6]);
    $this->assertEquals('DELETE FROM `ChildTable` WHERE (`OID` = "1")', $this->PDO->queries[7]);
    $this->assertEquals('DELETE FROM `ParentTable` WHERE (`OID` = "1")', $this->PDO->queries[8]);
  }

  /**
   * @covers DataMapper::recover
   * @covers DataMapper::setStateObject
   */
  public function testRecover(){
    $o = ParentMock::getProxy(5);
    $this->PDO->restore[] = new RowPDOMock([['a' => 10, 'OID' => 5]]);
    $this->object->recover($o);
    $this->assertEquals('SELECT ParentTable.af AS a,ParentTable.OID AS OID FROM `ParentTable`  WHERE (ParentTable.OID = "5")', $this->PDO->queries[0]);
    $this->assertEquals(10, $o->getA());
    $this->assertEquals(5, $o->getOID());
    $this->setUp();
    $o = ParentMock::getProxy(5);
    $this->PDO->restore[] = new RowPDOMock([['a' => '$/PPHP/tests/tools/classes/standard/storage/database/ORM/ParentMock:1', 'OID' => 5]]);
    $this->PDO->restore[] = new RowPDOMock([['a' => 10, 'OID' => 5]]);
    $this->object->recover($o);
    $this->assertEquals('SELECT ParentTable.af AS a,ParentTable.OID AS OID FROM `ParentTable`  WHERE (ParentTable.OID = "5")', $this->PDO->queries[0]);
    $this->assertEquals('SELECT ParentTable.af AS a,ParentTable.OID AS OID FROM `ParentTable`  WHERE (ParentTable.OID = "1")', $this->PDO->queries[1]);
    $this->assertInstanceOf('\PPHP\tests\tools\classes\standard\storage\database\ORM\ParentMock', $o->getA());
    $this->assertEquals(1, $o->getA()->getOID());
    $this->assertEquals(10, $o->getA()->getA());
    $this->setUp();
    $o = ChildMock::getProxy(5);
    $this->PDO->restore[] = new RowPDOMock([['a' => '$/PPHP/tests/tools/classes/standard/storage/database/ORM/ChildMock:1', 'd' => ChildMock::getProxy(2)->interpretation(), 'e' => 12, 'f' => 13, 'h' => '', 'OID' => 5]]);
    $this->PDO->restore[] = new RowPDOMock([['a' => 10, 'd' => ChildMock::getProxy(2), 'e' => 12, 'f' => 13, 'h' => '', 'OID' => 1]]);
    $this->object->recover($o);
    $this->assertEquals('SELECT ChildTable.df AS d,ChildTable.ef AS e,ChildTable.ff AS f,ChildTable.hf AS h,ParentTable.af AS a,ChildTable.OID AS OID FROM `ChildTable` INNER JOIN `ParentTable` ON (ChildTable.OID = ParentTable.OID) WHERE (ChildTable.OID = "5")', $this->PDO->queries[0]);
    $this->assertEquals('SELECT ChildTable.df AS d,ChildTable.ef AS e,ChildTable.ff AS f,ChildTable.hf AS h,ParentTable.af AS a,ChildTable.OID AS OID FROM `ChildTable` INNER JOIN `ParentTable` ON (ChildTable.OID = ParentTable.OID) WHERE (ChildTable.OID = "1")', $this->PDO->queries[1]);
    $this->assertEquals(1, $o->getA()->getOID());
    $this->assertEquals(10, $o->getA()->getA());
    $this->assertEquals(2, $o->getD()->getOID());
    $this->assertEquals(12, $o->getE());
    $this->assertEquals(13, $o->f);
    $this->assertInstanceOf('\PPHP\tools\patterns\database\associations\LongAssociation', $o->g);
    $this->assertEquals(0, $o->g->count());
    $this->assertEquals('SELECT ChildTable.df AS d,ChildTable.ef AS e,ChildTable.ff AS f,ChildTable.hf AS h,ParentTable.af AS a,ChildTable.OID AS OID FROM `ChildTable` INNER JOIN `ParentTable` ON (ChildTable.OID = ParentTable.OID) WHERE (ChildTable.hf = "$/PPHP/tests/tools/classes/standard/storage/database/ORM/ChildMock:5")', $o->g->getSelectQuery()->interpretation());
  }

  /**
   * @covers DataMapper::recoverFinding
   */
  public function testRecoverFinding(){
    $o = new ParentMock;
    $this->PDO->restore[] = new RowPDOMock([['a' => 10, 'OID' => 1]]);
    $this->object->recoverFinding($o, [['a', '=', '10']]);
    $this->assertEquals('SELECT ParentTable.af AS a,ParentTable.OID AS OID FROM `ParentTable`  WHERE (ParentTable.af = "10")', $this->PDO->queries[0]);
    $this->assertEquals(10, $o->getA());
    $this->assertEquals(1, $o->getOID());
    $this->setUp();
    $o = new ParentMock;
    $this->PDO->restore[] = new RowPDOMock([['a' => 10, 'OID' => 1]]);
    $this->object->recoverFinding($o, [['OID', '=', '1']]);
    $this->assertEquals('SELECT ParentTable.af AS a,ParentTable.OID AS OID FROM `ParentTable`  WHERE (ParentTable.OID = "1")', $this->PDO->queries[0]);
    $this->assertEquals(10, $o->getA());
    $this->assertEquals(1, $o->getOID());
    $this->setUp();
    $o = new ParentMock;
    $this->PDO->restore[] = new RowPDOMock([['a' => '$/PPHP/tests/tools/classes/standard/storage/database/ORM/ParentMock:1', 'OID' => 2]]);
    $this->PDO->restore[] = new RowPDOMock([['OID' => 1, 'a' => 10]]);
    $this->object->recoverFinding($o, [['a', '=', ParentMock::getProxy(1)]]);
    $this->assertEquals('SELECT ParentTable.af AS a,ParentTable.OID AS OID FROM `ParentTable`  WHERE (ParentTable.af = "$/PPHP/tests/tools/classes/standard/storage/database/ORM/ParentMock:1")', $this->PDO->queries[0]);
    $this->assertEquals('SELECT ParentTable.af AS a,ParentTable.OID AS OID FROM `ParentTable`  WHERE (ParentTable.OID = "1")', $this->PDO->queries[1]);
    $this->assertInstanceOf('\PPHP\tests\tools\classes\standard\storage\database\ORM\ParentMock', $o->getA());
    $this->assertEquals(1, $o->getA()->getOID());
    $this->assertEquals(10, $o->getA()->getA());
    $this->assertEquals(2, $o->getOID());
  }

  /**
   * @covers DataMapper::recoverGroupFinding
   */
  public function testRecoverGroupFinding(){
    $this->PDO->restore[] = new RowPDOMock([['a' => 10, 'OID' => 1], ['a' => 10, 'OID' => 2]]);
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
    $o = ChildMock::getProxy(1);
    $this->PDO->restore[] = new RowPDOMock([['a' => 10, 'd' => 11, 'e' => 12, 'f' => 13, 'h' => '', 'OID' => 1]]);
    $this->object->recover($o);
    $this->assertEquals('SELECT ChildTable.df AS d,ChildTable.ef AS e,ChildTable.ff AS f,ChildTable.hf AS h,ParentTable.af AS a,ChildTable.OID AS OID FROM `ChildTable` INNER JOIN `ParentTable` ON (ChildTable.OID = ParentTable.OID) WHERE (ChildTable.OID = "1")', $this->PDO->queries[0]);
    $this->assertInstanceOf('\PPHP\tools\patterns\database\associations\LongAssociation', $o->g);
    $this->assertEquals(0, $o->g->count());
    $this->PDO->restore[] = new RowPDOMock([['a' => 10, 'd' => 11, 'e' => 12, 'f' => 13, 'h' => '$/PPHP/tests/tools/classes/standard/storage/database/ORM/ChildMock:1', 'OID' => 2], ['a' => 10, 'd' => 11, 'e' => 12, 'f' => 13, 'h' => '$/PPHP/tests/tools/classes/standard/storage/database/ORM/ChildMock:1', 'OID' => 3]]);
    $this->object->recoverAssoc($o->g);
    $this->assertEquals('SELECT ChildTable.df AS d,ChildTable.ef AS e,ChildTable.ff AS f,ChildTable.hf AS h,ParentTable.af AS a,ChildTable.OID AS OID FROM `ChildTable` INNER JOIN `ParentTable` ON (ChildTable.OID = ParentTable.OID) WHERE (ChildTable.hf = "$/PPHP/tests/tools/classes/standard/storage/database/ORM/ChildMock:1")', $this->PDO->queries[1]);
    $this->assertEquals(2, $o->g->count());
    $o->g->rewind();
    $object = $o->g->current();
    $this->assertInstanceOf('\PPHP\tests\tools\classes\standard\storage\database\ORM\ParentMock', $object);
    $this->assertEquals(2, $object->getOID());
    $this->assertEquals(10, $object->getA());
    $o->g->next();
    $object = $o->g->current();
    $this->assertInstanceOf('\PPHP\tests\tools\classes\standard\storage\database\ORM\ParentMock', $object);
    $this->assertEquals(3, $object->getOID());
    $this->assertEquals(10, $object->getA());
  }
}
