<?php
namespace PPHP\tests\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\database\query as query;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class MultiConditionTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers query\MultiCondition::__construct
   */
  public function testConstruct(){
    $l = new query\LogicOperation(new query\Field('a'), '=', 'a');
    $r = new query\LogicOperation(new query\Field('b'), '=', 'b');
    $m = new query\MultiCondition($l, 'AND', $r);
    new query\MultiCondition($m, 'OR', new query\LogicOperation(new query\Field('c'), '=', 'c'));
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new query\MultiCondition($l, 'x', $r);
  }

  /**
   * @covers query\MultiCondition::interpretation
   */
  public function testInterpretation(){
    $l = new query\LogicOperation(new query\Field('a'), '=', 'a');
    $r = new query\LogicOperation(new query\Field('b'), '=', 'b');
    $m = new query\MultiCondition($l, 'AND', $r);
    $this->assertEquals('((`a` = "a") AND (`b` = "b"))', $m->interpretation());
    $m = new query\MultiCondition($m, 'OR', new query\LogicOperation(new query\Field('c'), '=', 'c'));
    $this->assertEquals('(((`a` = "a") AND (`b` = "b")) OR (`c` = "c"))', $m->interpretation());
  }

  /**
   * @covers query\MultiCondition::reestablish
   */
  public function testReestablish(){
    $m = query\MultiCondition::reestablish('(((`fieldA` = "1") AND (`fieldB` = "2")) OR (`fieldC` = "3"))');
    $this->assertEquals('OR', $m->getLogicOperator());
    $this->assertEquals('fieldC', $m->getRightOperand()->getField()->getName());
    $this->assertEquals('1', $m->getLeftOperand()->getLeftOperand()->getValue());
  }

  /**
   * @covers query\MultiCondition::isReestablish
   */
  public function testIsReestablish(){
    $this->assertTrue(query\MultiCondition::isReestablish('((`fieldA` = "1") AND (`fieldB` = "2"))'));
    $this->assertTrue(query\MultiCondition::isReestablish('(((`fieldA` = "1") AND (`fieldB` = "2")) OR (`fieldC` = "3"))'));
    $this->assertTrue(query\MultiCondition::isReestablish('((table.fieldA = "1")
                                                            AND (`fieldB` = "2"))'));
    $this->assertFalse(query\MultiCondition::isReestablish('((`fieldA` = "1"))'));
    $this->assertFalse(query\MultiCondition::isReestablish('((`fieldA` = "1") (`fieldB` = "2"))'));
    $this->assertFalse(query\MultiCondition::isReestablish('((`fieldA` = "1") AND `fieldB` = "2")'));
    $this->assertFalse(query\MultiCondition::isReestablish('(`fieldA` = "1") AND (`fieldB` = "2")'));
  }
}
