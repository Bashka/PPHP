<?php
namespace PPHP\tests\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use \PPHP\tools\patterns\database\query as query;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class LogicOperationTest extends \PHPUnit_Framework_TestCase {
  /**
   * @covers query\LogicOperation::__construct
   */
  public function testConstruct(){
    $o = new query\LogicOperation(new query\Field('test'), '=', 1);
    $this->assertEquals('test', $o->getField()->getName());
    $this->assertEquals('=', $o->getOperator());
    $this->assertEquals(1, $o->getValue());

    new query\LogicOperation(new query\Field('fieldA'), '=', new query\Field('fieldB'));

    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new query\LogicOperation(new query\Field('test'), '+', 1);
    new query\LogicOperation(new query\Field('test'), '=', null);
  }

  /**
   * @covers query\LogicOperation::interpretation
   */
  public function testInterpretation(){
    $o = new query\LogicOperation(new query\Field('test'), '=', 1);
    $this->assertEquals('(`test` = "1")', $o->interpretation());

    $o = new query\LogicOperation(new query\Field('fieldA'), '=', new query\Field('fieldB'));
    $this->assertEquals('(`fieldA` = `fieldB`)', $o->interpretation());
  }

  /**
   * @covers query\LogicOperation::reestablish
   */
  public function testReestablish(){
    $l = query\LogicOperation::reestablish('(`field` = "1")');
    $this->assertEquals('field', $l->getField()->getName());
    $this->assertEquals('=', $l->getOperator());
    $this->assertEquals('1', $l->getValue());

    $l = query\LogicOperation::reestablish('(`field` = "Hello world")');
    $this->assertEquals('field', $l->getField()->getName());
    $this->assertEquals('=', $l->getOperator());
    $this->assertEquals('Hello world', $l->getValue());

    $l = query\LogicOperation::reestablish('(`fieldA` = `fieldB`)');
    $this->assertEquals('fieldA', $l->getField()->getName());
    $this->assertEquals('=', $l->getOperator());
    $this->assertEquals('fieldB', $l->getValue()->getName());

    $l = query\LogicOperation::reestablish('(tableA.fieldA = tableB.fieldB)');
    $this->assertEquals('fieldA', $l->getField()->getName());
    $this->assertEquals('=', $l->getOperator());
    $this->assertEquals('tableB', $l->getValue()->getTable()->getTableName());
  }

  /**
   * @covers query\LogicOperation::isReestablish
   */
  public function testIsReestablish(){
    $this->assertTrue(query\LogicOperation::isReestablish('(`test` = "1")'));
    $this->assertTrue(query\LogicOperation::isReestablish('(`test` = `test`)'));
    $this->assertTrue(query\LogicOperation::isReestablish('(`test` = table.field)'));
    $this->assertTrue(query\LogicOperation::isReestablish('(table.field = "1")'));
    $this->assertTrue(query\LogicOperation::isReestablish('(table.field = `test`)'));
    $this->assertTrue(query\LogicOperation::isReestablish('(table.field = table.field)'));
    $this->assertTrue(query\LogicOperation::isReestablish('(`test` = "")'));

    $this->assertFalse(query\LogicOperation::isReestablish('`test` = "1"'));
    $this->assertFalse(query\LogicOperation::isReestablish('(= "1")'));
    $this->assertFalse(query\LogicOperation::isReestablish('(`test` "1")'));
  }
}
