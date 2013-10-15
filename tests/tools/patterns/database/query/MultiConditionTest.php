<?php
namespace PPHP\tests\tools\patterns\database\query;

use PPHP\tools\patterns\database\query\Field;
use PPHP\tools\patterns\database\query\LogicOperation;
use PPHP\tools\patterns\database\query\MultiCondition;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class MultiConditionTest extends \PHPUnit_Framework_TestCase{
  /**
   * Должен определять все компоненты логического выражения.
   * @covers PPHP\tools\patterns\database\query\MultiCondition::__construct
   */
  public function testShouldSetAllConditionAndOperator(){
    $loName = new LogicOperation(new Field('name'), '=', 'ivan');
    $loOID = new LogicOperation(new Field('OID'), '<', '10');
    $o = new MultiCondition($loName, 'AND', $loOID);
    $this->assertEquals($loName, $o->getLeftOperand());
    $this->assertEquals($loOID, $o->getRightOperand());
    $this->assertEquals('AND', $o->getLogicOperator());
  }

  /**
   * В качестве оператора может выступать только одно из следующих значений: AND или OR.
   * @covers PPHP\tools\patterns\database\query\MultiCondition::__construct
   */
  public function testOperatorShouldBeANDorOR(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new MultiCondition(new LogicOperation(new Field('name'), '=', 'ivan'), 'TEST', new LogicOperation(new Field('OID'), '<', '10'));
  }

  /**
   * Должен возвращать строку вида: ((условие) оператор (условие)).
   * @covers PPHP\tools\patterns\database\query\MultiCondition::interpretation
   */
  public function testShouldInterpretation(){
    $loName = new LogicOperation(new Field('name'), '=', 'ivan');
    $loOID = new LogicOperation(new Field('OID'), '<', '10');
    $o = new MultiCondition($loName, 'AND', $loOID);
    $this->assertEquals('((`name` = "ivan") AND (`OID` < "10"))', $o->interpretation());
    $o = new MultiCondition($o, 'OR', new LogicOperation(new Field('c'), '=', 'c'));
    $this->assertEquals('(((`name` = "ivan") AND (`OID` < "10")) OR (`c` = "c"))', $o->interpretation());
  }

  /**
   * Должен восстанавливаться из строки вида: ((условие) оператор (условие)).
   * @covers PPHP\tools\patterns\database\query\MultiCondition::reestablish
   */
  public function testShouldRestorableForString(){
    /**
     * @var \PPHP\tools\patterns\database\query\MultiCondition $o
     */
    $o = MultiCondition::reestablish('((`a` = "a") AND (`b` = "b"))');
    /**
     * @var \PPHP\tools\patterns\database\query\LogicOperation $c
     */
    $c = $o->getLeftOperand();
    $this->assertEquals('a', $c->getValue());
    $this->assertEquals('=', $c->getOperator());
    $this->assertEquals('AND', $o->getLogicOperator());
    /**
     * @var \PPHP\tools\patterns\database\query\MultiCondition $o
     */
    $o = MultiCondition::reestablish('((`a` = "a") OR (`b` = "b"))');
    /**
     * @var \PPHP\tools\patterns\database\query\LogicOperation $c
     */
    $c = $o->getLeftOperand();
    $this->assertEquals('a', $c->getValue());
    $this->assertEquals('=', $c->getOperator());
    $this->assertEquals('OR', $o->getLogicOperator());
    /**
     * @var \PPHP\tools\patterns\database\query\MultiCondition $o
     */
    $o = MultiCondition::reestablish('(((`fieldA` = "1") AND (`fieldB` = "2")) OR (`fieldC` = "3"))');
    $this->assertEquals('OR', $o->getLogicOperator());
    /**
     * @var \PPHP\tools\patterns\database\query\LogicOperation $c
     */
    $c = $o->getRightOperand();
    $this->assertEquals('fieldC', $c->getField()->getName());
    /**
     * @var \PPHP\tools\patterns\database\query\MultiCondition $c
     */
    $c = $o->getLeftOperand();
    $c = $c->getLeftOperand();
    /**
     * @var \PPHP\tools\patterns\database\query\LogicOperation $c
     */
    $this->assertEquals('1', $c->getValue());
  }

  /**
   * Допустимой строкой является строка вида: ((условие) оператор (условие)).
   * @covers PPHP\tools\patterns\database\query\MultiCondition::isReestablish
   */
  public function testGoodString(){
    $this->assertTrue(MultiCondition::isReestablish('((`fieldA` = "1") AND (`fieldB` = "2"))'));
    $this->assertTrue(MultiCondition::isReestablish('(((`fieldA` = "1") AND (`fieldB` = "2")) OR (`fieldC` = "3"))'));
    $this->assertTrue(MultiCondition::isReestablish('((table.fieldA = "1")
                                                            AND (`fieldB` = "2"))'));
  }

  /**
   * Должен возвращать false при недопустимой структуре строки.
   * @covers PPHP\tools\patterns\database\query\MultiCondition::isReestablish
   */
  public function testBedString(){
    $this->assertFalse(MultiCondition::isReestablish('((`fieldA` = "1"))'));
    $this->assertFalse(MultiCondition::isReestablish('((`fieldA` = "1") (`fieldB` = "2"))'));
    $this->assertFalse(MultiCondition::isReestablish('((`fieldA` = "1") AND `fieldB` = "2")'));
    $this->assertFalse(MultiCondition::isReestablish('(`fieldA` = "1") AND (`fieldB` = "2")'));
  }
}
