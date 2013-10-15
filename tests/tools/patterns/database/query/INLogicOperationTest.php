<?php
namespace PPHP\tests\tools\patterns\database\query;

use PPHP\tools\patterns\database\query\Field;
use PPHP\tools\patterns\database\query\INLogicOperation;
use PPHP\tools\patterns\database\query\Select;
use PPHP\tools\patterns\database\query\Table;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class INLogicOperationTest extends \PHPUnit_Framework_TestCase{
  /**
   * Должен определять сравниваемое поле.
   * @covers PPHP\tools\patterns\database\query\INLogicOperation::__construct
   */
  public function testShouldSetField(){
    $f = new Field('test');
    $i = new INLogicOperation($f);
    $this->assertEquals($f, $i->getField());
  }

  /**
   * Должен добавлять значение в контрольный список.
   * @covers PPHP\tools\patterns\database\query\INLogicOperation::addValue
   */
  public function testShouldAddValueInList(){
    $i = new INLogicOperation(new Field('test'));
    $i->addValue(5);
    $this->assertEquals(5, $i->getValues()[0]);
  }

  /**
   * В качестве значения могут выступать следующие типы: integer, float, boolean, string.
   * @covers PPHP\tools\patterns\database\query\INLogicOperation::addValue
   */
  public function testValueCanBeIntegerFloatBooleanString(){
    $i = new INLogicOperation(new Field('test'));
    $i->addValue(5);
    $i->addValue('a');
    $i->addValue(true);
    $i->addValue(1.1);
    $this->assertEquals(5, $i->getValues()[0]);
  }

  /**
   * Должен выбрасывать исключение если передан неверный тип.
   * @covers PPHP\tools\patterns\database\query\INLogicOperation::addValue
   */
  public function testShouldThrowExceptionIfBadValue(){
    $i = new INLogicOperation(new Field('test'));
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    $i->addValue([1, 2, 3]);
  }

  /**
   * Должен определять инструкцию Select в качестве источника допустимых значений.
   * @covers PPHP\tools\patterns\database\query\INLogicOperation::setSelectQuery
   */
  public function testShouldSetSelect(){
    $i = new INLogicOperation(new Field('test'));
    $s = new Select();
    $i->setSelectQuery($s);
    $this->assertEquals($s, $i->getSelectQuery());
  }

  /**
   * Должен возвращать строку вида: имяПоля IN ((значение[, значение]*)|(selectИнструкция)).
   * @covers PPHP\tools\patterns\database\query\INLogicOperation::interpretation
   */
  public function testShouldInterpretation(){
    $i = new INLogicOperation(new Field('test'));
    $i->addValue(5);
    $i->addValue('a');
    $i->addValue(true);
    $i->addValue(1.1);
    $this->assertEquals('(`test` IN ("5","a","true","1.1"))', $i->interpretation());
    $i = new INLogicOperation(new Field('test'));
    $s = new Select();
    $s->addAllField();
    $s->addTable(new Table('table'));
    $i->setSelectQuery($s);
    $this->assertEquals('(`test` IN (SELECT * FROM `table`))', $i->interpretation());
  }

  /**
   * Должен выбрасывать исключение в случае отсутствия хотя бы одного допустимого значения.
   * @covers PPHP\tools\patterns\database\query\INLogicOperation::interpretation
   */
  public function testShouldThrowExceptionIfNotValues(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException');
    $i = new INLogicOperation(new Field('test'));
    $i->interpretation();
  }

  /**
   * Инструкция Select имеет больший приоритет.
   * @covers PPHP\tools\patterns\database\query\INLogicOperation::interpretation
   */
  public function testShouldResetValues(){
    $i = new INLogicOperation(new Field('test'));
    $i->addValue(5);
    $s = new Select;
    $s->addTable(new Table('test'));
    $s->addAllField();
    $i->setSelectQuery($s);
    $this->assertEquals('(`test` IN (SELECT * FROM `test`))', $i->interpretation());
  }

  /**
   * Должен восстанавливаться из строки вида: имяПоля IN ((значение[, значение]*)).
   * @covers PPHP\tools\patterns\database\query\INLogicOperation::reestablish
   */
  public function testShouldRestorableForString(){
    $o = INLogicOperation::reestablish('(table.field IN ("a", "b", "1"))');
    $this->assertEquals('field', $o->getField()->getName());
    $this->assertEquals('a', $o->getValues()[0]);
  }

  /**
   * Допустимой строкой является строка вида: имяПоля IN ((значение[, значение]*)).
   * @covers PPHP\tools\patterns\database\query\INLogicOperation::isReestablish
   */
  public function testGoodString(){
    $this->assertTrue(INLogicOperation::isReestablish('(`field` IN ("a"))'));
    $this->assertTrue(INLogicOperation::isReestablish('(table.field IN ("a","b", "1"))'));
    $this->assertTrue(INLogicOperation::isReestablish('(table.field IN
                                                              ("a",
                                                              "b",
                                                              "1"))'));
  }

  /**
   * Должен возвращать false при недопустимой структуре строки.
   * @covers PPHP\tools\patterns\database\query\INLogicOperation::isReestablish
   */
  public function testBedString(){
    $this->assertFalse(INLogicOperation::isReestablish('`field` IN ("a")'));
    $this->assertFalse(INLogicOperation::isReestablish('(`field` ("a"))'));
    $this->assertFalse(INLogicOperation::isReestablish('(`field` IN "a")'));
    $this->assertFalse(INLogicOperation::isReestablish('(`field` IN ("a" "b"))'));
  }
}
