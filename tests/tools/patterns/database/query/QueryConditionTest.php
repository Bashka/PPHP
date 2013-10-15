<?php
namespace PPHP\tests\tools\patterns\database\query;

use PPHP\tools\patterns\database\query\AndMultiCondition;
use PPHP\tools\patterns\database\query\Field;
use PPHP\tools\patterns\database\query\LogicOperation;
use PPHP\tools\patterns\database\query\OrMultiCondition;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class QueryConditionTest extends \PHPUnit_Framework_TestCase{
  /**
   * Должен добавлять добавлять условие в выражение.
   * @covers PPHP\tools\patterns\database\query\QueryCondition::addCondition
   */
  public function testShouldAddCondition(){
    $qc = new AndMultiCondition;
    $loName = new LogicOperation(new Field('name'), '=', 'ivan');
    $loOID = new LogicOperation(new Field('OID'), '<', '10');
    $qc->addCondition($loName);
    $qc->addCondition($loOID);
    $this->assertEquals($loName, $qc->getConditions()[0]);
    $this->assertEquals($loOID, $qc->getConditions()[1]);
  }

  /**
   * Должен возвращать строку вида: ((условие) оператор (условие)[ оператор (условие)]*).
   * @covers PPHP\tools\patterns\database\query\QueryCondition::interpretation
   * @covers PPHP\tools\patterns\database\query\AndMultiCondition::interpretation
   * @covers PPHP\tools\patterns\database\query\OrMultiCondition::interpretation
   */
  public function testShouldInterpretation(){
    $qc = new AndMultiCondition;
    $loName = new LogicOperation(new Field('name'), '=', 'ivan');
    $loOID = new LogicOperation(new Field('OID'), '<', '10');
    $qc->addCondition($loName);
    $qc->addCondition($loOID);
    $this->assertEquals('((`name` = "ivan") AND (`OID` < "10"))', $qc->interpretation());
  }

  /**
   * Должен выбрасывать исключение, если на номент вызова добавлено менее двух условий.
   * @covers PPHP\tools\patterns\database\query\QueryCondition::interpretation
   */
  public function testShouldThrowExceptionIfNotConditions(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException');
    $qc = new AndMultiCondition;
    $qc->interpretation();
  }

  /**
   * Должен восстанавливаться из строки вида: (условие) оператор (условие)[ оператор (условие)]*.
   * @covers PPHP\tools\patterns\database\query\QueryCondition::reestablish
   */
  public function testShouldRestorableForString(){
    /**
     * @var \PPHP\tools\patterns\database\query\AndMultiCondition $o
     */
    $o = AndMultiCondition::reestablish('((`a` = "a") AND (`b` = "b") AND (`c` = "c"))');
    /**
     * @var \PPHP\tools\patterns\database\query\LogicOperation $c
     */
    $c = $o->getConditions()[0];
    $this->assertEquals('a', $c->getValue());
    $this->assertEquals('=', $c->getOperator());
    /**
     * @var \PPHP\tools\patterns\database\query\OrMultiCondition $o
     */
    $o = OrMultiCondition::reestablish('((`a` = "a") OR (`b` = "b") OR (`c` = "c"))');
    /**
     * @var \PPHP\tools\patterns\database\query\LogicOperation $c
     */
    $c = $o->getConditions()[0];
    $this->assertEquals('a', $c->getValue());
    $this->assertEquals('=', $c->getOperator());
  }

  /**
   * Допустимой строкой является строка вида: (условие) оператор (условие)[ оператор (условие)]*.
   * @covers PPHP\tools\patterns\database\query\QueryCondition::isReestablish
   */
  public function testGoodString(){
    $this->assertTrue(OrMultiCondition::isReestablish('((`a` = "a") OR (`b` = "b"))'));
    $this->assertTrue(AndMultiCondition::isReestablish('((`a` = "a") AND (`b` = "b") AND (`c` = "c"))'));
    $this->assertTrue(OrMultiCondition::isReestablish('((`a` = "a") OR (`b` = "b") OR (`c` = "c"))'));
    $this->assertTrue(OrMultiCondition::isReestablish('(
                                                                (
                                                                  (`a` = "a") AND
                                                                  (`b` = "b") AND
                                                                  (`c` = "c")
                                                                ) OR
                                                                (`b` = "b") OR
                                                                (`c` = "c")
                                                              )'));
  }

  /**
   * Должен возвращать false при недопустимой структуре строки.
   * @covers PPHP\tools\patterns\database\query\QueryCondition::isReestablish
   */
  public function testBedString(){
    $this->assertFalse(AndMultiCondition::isReestablish('((`a` = "a") OR (`b` = "b"))'));
    $this->assertFalse(AndMultiCondition::isReestablish('((`a` = "a") (`b` = "b"))'));
    $this->assertFalse(AndMultiCondition::isReestablish('((`a` = "a"))'));
  }
}
