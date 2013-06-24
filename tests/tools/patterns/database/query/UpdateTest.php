<?php
namespace PPHP\tests\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\database\query as query;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class UpdateTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers query\Update::__construct
   */
  public function testConstruct(){
    $o = new query\Update(new query\Table('table'));
    $this->assertEquals('table', $o->getTable()->getTableName());
  }

  /**
   * @covers query\Update::interpretation
   */
  public function testInterpretation(){
    $o = new query\Update(new query\Table('table'));
    $o->addData(new query\Field('fieldA'), "1");
    $o->addData(new query\Field('fieldB'), "2");
    $o->addData(new query\Field('fieldC'), "3");
    $this->assertEquals('UPDATE `table` SET `fieldA` = "1",`fieldB` = "2",`fieldC` = "3"', $o->interpretation());
    $o->insertWhere(new query\Where(new query\LogicOperation(new query\Field('fieldD'), '>', '5')));
    $this->assertEquals('UPDATE `table` SET `fieldA` = "1",`fieldB` = "2",`fieldC` = "3" WHERE (`fieldD` > "5")', $o->interpretation());
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException');
    $o = new query\Update(new query\Table('table'));
    $o->interpretation();
  }

  /**
   * @covers query\Update::reestablish
   */
  public function testReestablish(){
    $o = query\Update::reestablish('UPDATE `table` SET `fieldA` = "1", `fieldB` = "2", `fieldC` = "3" WHERE (`fieldD` > "5")');
    $this->assertEquals('UPDATE `table` SET `fieldA` = "1",`fieldB` = "2",`fieldC` = "3" WHERE (`fieldD` > "5")', $o->interpretation());
    $this->assertEquals('table', $o->getTable()->getTableName());
  }

  /**
   * @covers query\Update::isReestablish
   */
  public function testIsReestablish(){
    $this->assertTrue(query\Update::isReestablish('UPDATE `table` SET `fieldA` = "1"'));
    $this->assertTrue(query\Update::isReestablish('UPDATE `table` SET `fieldA` = "1",`fieldB` = "2", `fieldC` = "3"'));
    $this->assertTrue(query\Update::isReestablish('UPDATE `table` SET table.fieldA = "1", table.fieldB = "2"'));
    $this->assertTrue(query\Update::isReestablish('UPDATE `table`
                                                   SET `fieldA` = "1", `fieldB` = "2", `fieldC` = "3"
                                                   WHERE (`fieldD` > "5")'));
    $this->assertFalse(query\Update::isReestablish('`table` SET `fieldA` = "1",`fieldB` = "2", `fieldC` = "3"'));
    $this->assertFalse(query\Update::isReestablish('UPDATE `table` `fieldA` = "1",`fieldB` = "2", `fieldC` = "3"'));
    $this->assertFalse(query\Update::isReestablish('UPDATE `table` SET `fieldA` = "1" `fieldB` = "2"'));
    $this->assertFalse(query\Update::isReestablish('UPDATE `table` SET `fieldA` "1"'));
  }
}
