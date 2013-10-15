<?php
namespace PPHP\tests\tools\patterns\database\query\builder;

use PPHP\tools\patterns\database\query\builder\Where;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class WhereTest extends \PHPUnit_Framework_TestCase{
  protected function setUp(){
    Where::getInstance()->clear();
  }

  /**
   * Должен добавлять условие в стек.
   * @covers \PPHP\tools\patterns\database\query\builder\Where::create
   */
  public function testShouldAddConditionInStack(){
    $c = Where::getInstance()->create('id', '>', '5')->getConditions();
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\LogicOperation', $c->top());
    $this->assertEquals('id', $c->top()->getField()->getName());
    $this->assertEquals('>', $c->top()->getOperator());
    $this->assertEquals('5', $c->top()->getValue());
    $c = Where::getInstance()->create('id', '<', '10')->getConditions();
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\LogicOperation', $c->top());
    $this->assertEquals('id', $c->top()->getField()->getName());
    $this->assertEquals('<', $c->top()->getOperator());
    $this->assertEquals('10', $c->top()->getValue());
  }

  /**
   * Если третий параметр обрамлен в косые кавычки (`), он определяет объект Field.
   * @covers \PPHP\tools\patterns\database\query\builder\Where::create
   * @covers \PPHP\tools\patterns\database\query\builder\Where::orC
   * @covers \PPHP\tools\patterns\database\query\builder\Where::andC
   * @covers \PPHP\tools\patterns\database\query\builder\Where::createCondition
   */
  public function testRightOperandFieldIfWrapQuotes(){
    $c = Where::getInstance()->create('id', '=', '`name`')->getConditions();
    $this->assertEquals('name', $c->top()->getValue()->getName());
  }

  /**
   * Если третий параметр не обрамлен в косые кавычки (`), он определяет строковое значение.
   * @covers \PPHP\tools\patterns\database\query\builder\Where::create
   * @covers \PPHP\tools\patterns\database\query\builder\Where::orC
   * @covers \PPHP\tools\patterns\database\query\builder\Where::andC
   * @covers \PPHP\tools\patterns\database\query\builder\Where::createCondition
   */
  public function testRightOperandStringIfNoWrapQuotes(){
    $c = Where::getInstance()->create('id', '=', '5')->getConditions();
    $this->assertEquals('5', $c->top()->getValue());
  }

  /**
   * Если в качестве оператора указан in, то последний элемент может быть массивом с перечислением значений.
   * @covers \PPHP\tools\patterns\database\query\builder\Where::create
   * @covers \PPHP\tools\patterns\database\query\builder\Where::orC
   * @covers \PPHP\tools\patterns\database\query\builder\Where::andC
   * @covers \PPHP\tools\patterns\database\query\builder\Where::createCondition
   */
  public function testOperationInIfOperatorSetIn(){
    $c = Where::getInstance()->create('id', 'in', [1, 2, 3])->getConditions();
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\INLogicOperation', $c->top());
    $this->assertEquals([1, 2, 3], $c->top()->getValues());
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    Where::getInstance()->create('id', 'in', '1');
  }

  /**
   * В качестве первого параметра может быть только строка.
   * @covers \PPHP\tools\patterns\database\query\builder\Where::create
   * @covers \PPHP\tools\patterns\database\query\builder\Where::orC
   * @covers \PPHP\tools\patterns\database\query\builder\Where::andC
   * @covers \PPHP\tools\patterns\database\query\builder\Where::createCondition
   */
  public function testLeftOperandShouldBeString(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    Where::getInstance()->create(1, '=', '`id`');
  }

  /**
   * В качестве второго параметра может быть только одно из следующих значений: =, !=, >, <, >=, <=, in.
   * @covers \PPHP\tools\patterns\database\query\builder\Where::create
   * @covers \PPHP\tools\patterns\database\query\builder\Where::orC
   * @covers \PPHP\tools\patterns\database\query\builder\Where::andC
   * @covers \PPHP\tools\patterns\database\query\builder\Where::createCondition
   */
  public function testOperatorShouldBeLogicOperator(){
    Where::getInstance()->create('name', '=', 'test');
    Where::getInstance()->create('name', '!=', 'test');
    Where::getInstance()->create('name', '>=', 'test');
    Where::getInstance()->create('name', '<=', 'test');
    Where::getInstance()->create('name', '>', 'test');
    Where::getInstance()->create('name', '<', 'test');
    Where::getInstance()->create('name', 'in', [1]);
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    Where::getInstance()->create('name', '*', 'test');
  }

  /**
   * В качестве третьего параметра может быть только строка или массив.
   * @covers \PPHP\tools\patterns\database\query\builder\Where::create
   * @covers \PPHP\tools\patterns\database\query\builder\Where::orC
   * @covers \PPHP\tools\patterns\database\query\builder\Where::andC
   * @covers \PPHP\tools\patterns\database\query\builder\Where::createCondition
   */
  public function testRightOperandShouldBeStringOrArray(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    Where::getInstance()->create('id', '=', 1);
  }

  /**
   * Если в первом или песледнем параметре присутствует точка, должен добавлять информацию о таблице поля.
   * @covers \PPHP\tools\patterns\database\query\builder\Where::create
   * @covers \PPHP\tools\patterns\database\query\builder\Where::orC
   * @covers \PPHP\tools\patterns\database\query\builder\Where::andC
   * @covers \PPHP\tools\patterns\database\query\builder\Where::createCondition
   */
  public function testShouldAddTableFieldIfPointSet(){
    $c = Where::getInstance()->create('people.id', '=', '`people.name`')->getConditions();
    $this->assertEquals('people', $c->top()->getField()->getTable()->getTableName());
    $this->assertEquals('people', $c->top()->getValue()->getTable()->getTableName());
  }

  /**
   * Должен создавать логическое выражение для текущего условия с разделителем И и переданным условием в качестве правого операнда.
   * @covers \PPHP\tools\patterns\database\query\builder\Where::andC
   */
  public function testShouldCreateAndMultiCondition(){
    $c = Where::getInstance()->create('id', '>', '5')->andC('id', '<', '10')->getConditions();
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\MultiCondition', $c->top());
    $this->assertEquals('AND', $c->top()->getLogicOperator());
  }

  /**
   * Должен создавать логическое выражение для текущего условия с разделителем ИЛИ и переданным условием в качестве правого операнда.
   * @covers \PPHP\tools\patterns\database\query\builder\Where::orC
   */
  public function testShouldCreateOrMultiCondition(){
    $c = Where::getInstance()->create('id', '>', '5')->orC('id', '<', '10')->getConditions();
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\MultiCondition', $c->top());
    $this->assertEquals('OR', $c->top()->getLogicOperator());
  }

  /**
   * Если передан параметр, должен объединять два последних условных выражения с указанным в параметре разделителем.
   * @covers \PPHP\tools\patterns\database\query\builder\Where::last
   */
  public function testShouldCreateMultiConditionToTopStack(){
    $c = Where::getInstance()->create('id', '>', '5')->create('id', '<', '10')->andC('name', '=', 'test')->last('OR')->last();
    $this->assertEquals('WHERE ((`id` > "5") OR ((`id` < "10") AND (`name` = "test")))', $c->interpretation('mysql'));
  }

  /**
   * В качестве параметра может быть передана строка вида: AND или OR.
   * @covers \PPHP\tools\patterns\database\query\builder\Where::last
   */
  public function testArgShouldBeAndOr(){
    Where::getInstance()->create('id', '>', '5')->create('id', '<', '10')->last('OR');
    Where::getInstance()->create('id', '>', '5')->create('id', '<', '10')->last('AND');
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    Where::getInstance()->create('id', '>', '5')->create('id', '<', '10')->last('and');
  }
}
