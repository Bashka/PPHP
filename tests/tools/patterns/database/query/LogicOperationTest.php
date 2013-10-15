<?php
namespace PPHP\tests\tools\patterns\database\query;

use PPHP\tools\patterns\database\query\Field;
use PPHP\tools\patterns\database\query\LogicOperation;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class LogicOperationTest extends \PHPUnit_Framework_TestCase{
  /**
   * Должен определять условие.
   * @covers PPHP\tools\patterns\database\query\LogicOperation::__construct
   */
  public function testShouldSetCondition(){
    $o = new LogicOperation(new Field('test'), '=', 1);
    $this->assertEquals('test', $o->getField()->getName());
    $this->assertEquals('=', $o->getOperator());
    $this->assertEquals(1, $o->getValue());
  }

  /**
   * В качестве оператора допустимо одно из следующих значений: =, !=, >=, <=, >, <.
   * @covers PPHP\tools\patterns\database\query\LogicOperation::__construct
   */
  public function testOperatorMustBeMathOperator(){
    new LogicOperation(new Field('test'), '=', 1);
    new LogicOperation(new Field('test'), '!=', 1);
    new LogicOperation(new Field('test'), '>=', 1);
    new LogicOperation(new Field('test'), '<=', 1);
    new LogicOperation(new Field('test'), '>', 1);
    new LogicOperation(new Field('test'), '<', 1);
  }

  /**
   * Должен выбрасывать исключение при передаче в качестве оператора не допустимого значения.
   * @covers PPHP\tools\patterns\database\query\LogicOperation::__construct
   */
  public function testShouldThrowExceptionIfBadOperator(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new LogicOperation(new Field('test'), '*', 1);
  }

  /**
   * В качестве значения могут выступать следующие типы: integer, float, boolean, string, Field.
   * @covers PPHP\tools\patterns\database\query\LogicOperation::__construct
   */
  public function testValueMustBeIntegerFloatBooleanStringField(){
    new LogicOperation(new Field('fieldA'), '=', 'test');
    new LogicOperation(new Field('fieldA'), '=', 1);
    new LogicOperation(new Field('fieldA'), '=', 1.1);
    new LogicOperation(new Field('fieldA'), '=', true);
    new LogicOperation(new Field('fieldA'), '=', new Field('fieldB'));
  }

  /**
   * Должен выбрасывать исключение если в качестве значения передан неверный тип.
   * @covers PPHP\tools\patterns\database\query\LogicOperation::__construct
   */
  public function testShouldThrowExceptionIfBadValue(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new LogicOperation(new Field('test'), '=', null);
  }

  /**
   * Должен возвращать строку вида: (имяПоля оператор значение|имяПоля).
   * @covers PPHP\tools\patterns\database\query\LogicOperation::interpretation
   */
  public function testShouldInterpretation(){
    $o = new LogicOperation(new Field('test'), '=', 1);
    $this->assertEquals('(`test` = "1")', $o->interpretation());
    $o = new LogicOperation(new Field('fieldA'), '=', new Field('fieldB'));
    $this->assertEquals('(`fieldA` = `fieldB`)', $o->interpretation());
  }

  /**
   * Должен восстанавливаться из строки вида: (имяПоля оператор значение|имяПоля).
   * @covers PPHP\tools\patterns\database\query\LogicOperation::reestablish
   */
  public function testShouldRestorableForString(){
    /**
     * @var \PPHP\tools\patterns\database\query\LogicOperation $l
     */
    $l = LogicOperation::reestablish('(`field` = "1")');
    $this->assertEquals('field', $l->getField()->getName());
    $this->assertEquals('=', $l->getOperator());
    $this->assertEquals('1', $l->getValue());
    /**
     * @var \PPHP\tools\patterns\database\query\LogicOperation $l
     */
    $l = LogicOperation::reestablish('(`field` = "Hello world")');
    $this->assertEquals('field', $l->getField()->getName());
    $this->assertEquals('=', $l->getOperator());
    $this->assertEquals('Hello world', $l->getValue());
    /**
     * @var \PPHP\tools\patterns\database\query\LogicOperation $l
     */
    $l = LogicOperation::reestablish('(`fieldA` = `fieldB`)');
    $this->assertEquals('fieldA', $l->getField()->getName());
    $this->assertEquals('=', $l->getOperator());
    $this->assertEquals('fieldB', $l->getValue()->getName());
    /**
     * @var \PPHP\tools\patterns\database\query\LogicOperation $l
     */
    $l = LogicOperation::reestablish('(tableA.fieldA = tableB.fieldB)');
    $this->assertEquals('fieldA', $l->getField()->getName());
    $this->assertEquals('=', $l->getOperator());
    $this->assertEquals('tableB', $l->getValue()->getTable()->getTableName());
  }

  /**
   * Допустимой строкой является строка вида: имяПоля оператор значение|имяПоля.
   * @covers PPHP\tools\patterns\database\query\LogicOperation::isReestablish
   */
  public function testGoodString(){
    $this->assertTrue(LogicOperation::isReestablish('(`test` = "1")'));
    $this->assertTrue(LogicOperation::isReestablish('(`test` = `test`)'));
    $this->assertTrue(LogicOperation::isReestablish('(`test` = table.field)'));
    $this->assertTrue(LogicOperation::isReestablish('(table.field = "1")'));
    $this->assertTrue(LogicOperation::isReestablish('(table.field = `test`)'));
    $this->assertTrue(LogicOperation::isReestablish('(table.field = table.field)'));
    $this->assertTrue(LogicOperation::isReestablish('(`test` = "")'));
  }

  /**
   * Должен возвращать false при недопустимой структуре строки.
   * @covers PPHP\tools\patterns\database\query\LogicOperation::isReestablish
   */
  public function testBedString(){
    $this->assertFalse(LogicOperation::isReestablish('`test` = "1"'));
    $this->assertFalse(LogicOperation::isReestablish('(= "1")'));
    $this->assertFalse(LogicOperation::isReestablish('(`test` "1")'));
  }
}
