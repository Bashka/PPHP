<?php
namespace PPHP\tests\tools\patterns\database\query;

use PPHP\tools\patterns\database\query\Field as Field;
use PPHP\tools\patterns\database\query\Table as Table;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class FieldTest extends \PHPUnit_Framework_TestCase{
  /**
   * Должен идентифицировать объект именем поля.
   * @covers PPHP\tools\patterns\database\query\Field::__construct
   */
  public function testShouldInitObjectFieldName(){
    $o = new Field('name');
    $this->assertEquals('name', $o->getName());
  }

  /**
   * Именем поля может быть только строка.
   * @covers PPHP\tools\patterns\database\query\Field::__construct
   */
  public function testFieldNameStringIs(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new Field(1);
  }

  /**
   * Должен возвращать имя поля.
   * @covers PPHP\tools\patterns\database\query\Field::getName
   */
  public function testShouldReturnFieldName(){
    $o = new Field('name');
    $this->assertEquals('name', $o->getName());
  }

  /**
   * Должен устанавливать целевую таблицу.
   * @covers PPHP\tools\patterns\database\query\Field::setTable
   */
  public function testShouldSetTable(){
    $o = new Field('name');
    $o->setTable(new Table('people'));
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\Table', $o->getTable());
    $this->assertEquals('people', $o->getTable()->getTableName());
  }

  /**
   * Должен заменять целевую таблицу при повторном вызове.
   * @covers PPHP\tools\patterns\database\query\Field::setTable
   */
  public function testShouldResetTable(){
    $o = new Field('name');
    $o->setTable(new Table('people'));
    $o->setTable(new Table('student'));
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\Table', $o->getTable());
    $this->assertEquals('student', $o->getTable()->getTableName());
  }

  /**
   * Должен возвращать целевую таблицу, если она установлена.
   * @covers PPHP\tools\patterns\database\query\Field::getTable
   */
  public function testShouldReturnTableIfTableSet(){
    $o = new Field('name');
    $o->setTable(new Table('people'));
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\Table', $o->getTable());
  }

  /**
   * Должен возвращать null, если целевая таблица не установлена.
   * @covers PPHP\tools\patterns\database\query\Field::getTable
   */
  public function testShouldReturnNullIfTableSet(){
    $o = new Field('name');
    $this->assertEquals(null, $o->getTable());
  }

  /**
   * Если целевая таблица установлена, должен возвращать строку вида: имяТаблицы.имяПоля.
   * @covers PPHP\tools\patterns\database\query\Field::interpretation
   */
  public function testShouldInterpretationTableAndFieldIfTableSet(){
    $o = new Field('name');
    $o->setTable(new Table('people'));
    $this->assertEquals('people.name', $o->interpretation());
  }

  /**
   * Если целевая таблица не установлена, должен возвращать строку вида: `имяПоля`.
   * @covers PPHP\tools\patterns\database\query\Field::interpretation
   */
  public function testShouldInterpretationFieldIfTableEmpty(){
    $o = new Field('name');
    $this->assertEquals('`name`', $o->interpretation());
  }

  /**
   * Должен восстанавливаться из строки вида: `имяПоля` - и вида: имяТаблицы.имяПоля.
   * @covers PPHP\tools\patterns\database\query\Field::reestablish
   */
  public function testShouldRestorableForString(){
    $f = Field::reestablish('`test`');
    $this->assertEquals('test', $f->getName());
    $f = Field::reestablish('table.field');
    $this->assertEquals('field', $f->getName());
    $this->assertEquals('table', $f->getTable()->getTableName());
  }

  /**
   * Допустимой строкой является строка вида: `имяПоля` - и вида: имяТаблицы.имяПоля.
   * @covers PPHP\tools\patterns\database\query\Table::isReestablish
   */
  public function testGoodString(){
    $this->assertTrue(Field::isReestablish('`field`'));
    $this->assertTrue(Field::isReestablish('table.field'));
    $this->assertTrue(Field::isReestablish('`field5_`'));
    $this->assertTrue(Field::isReestablish('`_field`'));
  }

  /**
   * Должен возвращать false при недопустимой структуре строки.
   * @covers PPHP\tools\patterns\database\query\Table::isReestablish
   */
  public function testBedString(){
    $this->assertFalse(Field::isReestablish('1field'));
    $this->assertFalse(Field::isReestablish('field+'));
    $this->assertFalse(Field::isReestablish('tableA.tableB.field'));
    $this->assertFalse(Field::isReestablish('1table.field'));
  }
}
