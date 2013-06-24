<?php
namespace PPHP\tests\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\database\query as query;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class INLogicOperationTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers query\INLogicOperation::__construct
   */
  public function testConstruct(){
    $f = new query\Field('test');
    $i = new query\INLogicOperation($f);
    $this->assertEquals($f, $i->getField());
  }

  /**
   * @covers query\INLogicOperation::addValue
   */
  public function testAddValue(){
    $i = new query\INLogicOperation(new query\Field('test'));
    $i->addValue(5);
    $i->addValue('a');
    $i->addValue(true);
    $i->addValue(1.1);
    $this->assertEquals('test', $i->getField()->getName());
    $this->assertEquals(5, $i->getValues()[0]);
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    $i->addValue([1, 2, 3]);
  }

  /**
   * @covers query\INLogicOperation::setSelectQuery
   */
  public function testSetSelectQuery(){
    $i = new query\INLogicOperation(new query\Field('test'));
    $s = new query\Select();
    $i->setSelectQuery($s);
    $this->assertEquals($s, $i->getSelectQuery());
  }

  /**
   * @covers query\INLogicOperation::interpretation
   */
  public function testInterpretation(){
    $i = new query\INLogicOperation(new query\Field('test'));
    $i->addValue(5);
    $i->addValue('a');
    $i->addValue(true);
    $i->addValue(1.1);
    $this->assertEquals('(`test` IN ("5","a","true","1.1"))', $i->interpretation());
    $i = new query\INLogicOperation(new query\Field('test'));
    $s = new query\Select();
    $s->addAllField();
    $s->addTable(new query\Table('table'));
    $i->setSelectQuery($s);
    $this->assertEquals('(`test` IN ("SELECT * FROM `table`"))', $i->interpretation());
  }

  /**
   * @covers query\INLogicOperation::reestablish
   */
  public function testReestablish(){
    $o = query\INLogicOperation::reestablish('(table.field IN ("a", "b", "1"))');
    $this->assertEquals('field', $o->getField()->getName());
    $this->assertEquals('a', $o->getValues()[0]);
  }

  /**
   * @covers query\INLogicOperation::isReestablish
   */
  public function testIsReestablish(){
    $this->assertTrue(query\INLogicOperation::isReestablish('(`field` IN ("a"))'));
    $this->assertTrue(query\INLogicOperation::isReestablish('(table.field IN ("a","b", "1"))'));
    $this->assertTrue(query\INLogicOperation::isReestablish('(table.field IN
                                                              ("a",
                                                              "b",
                                                              "1"))'));
    $this->assertFalse(query\INLogicOperation::isReestablish('`field` IN ("a")'));
    $this->assertFalse(query\INLogicOperation::isReestablish('(`field` ("a"))'));
    $this->assertFalse(query\INLogicOperation::isReestablish('(`field` IN "a")'));
    $this->assertFalse(query\INLogicOperation::isReestablish('(`field` IN ("a" "b"))'));
  }
}
