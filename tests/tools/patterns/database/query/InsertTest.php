<?php
namespace PPHP\tests\tools\patterns\database\query;

use PPHP\tools\patterns\database\query\Field;
use PPHP\tools\patterns\database\query\Insert;
use PPHP\tools\patterns\database\query\Select;
use PPHP\tools\patterns\database\query\Table;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class InsertTest extends \PHPUnit_Framework_TestCase{
  /**
   * Должен определять целевую таблицу.
   * @covers PPHP\tools\patterns\database\query\Insert::__construct
   */
  public function testShouldSetTable(){
    $o = new Insert(new Table('table'));
    $this->assertEquals('table', $o->getTable()->getTableName());
  }

  /**
   * Должен добавлять значению полю.
   * @covers PPHP\tools\patterns\database\query\Insert::addData
   */
  public function testShouldAddFieldAndValue(){
    $o = new Insert(new Table('table'));
    $o->addData(new Field('name'), 'ivan');
    $this->assertEquals('name', $o->getFields()[0]->getName());
    $this->assertEquals('ivan', $o->getValues()[0]);
  }

  /**
   * В качестве значения могут выступать данные следующих типов: number, string, boolean.
   * @covers PPHP\tools\patterns\database\query\Insert::addData
   */
  public function testValueShouldBeStringNumberBoolean(){
    (new Insert(new Table('table')))->addData(new Field('name'), 'text');
    (new Insert(new Table('table')))->addData(new Field('name'), 1);
    (new Insert(new Table('table')))->addData(new Field('name'), 1.1);
    (new Insert(new Table('table')))->addData(new Field('name'), true);
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    (new Insert(new Table('table')))->addData(new Field('name'), []);
  }

  /**
   * Должен устанавливать в качестве значения вложенный запрос.
   * @covers PPHP\tools\patterns\database\query\Insert::setSelect
   */
  public function testShouldSetInsertedSelect(){
    $s = new Select;
    $s->addAllField();
    $s->addTable(new Table('people'));
    $o = new Insert(new Table('table'));
    $o->setSelect($s);
    $this->assertEquals($s, $o->getSelect());
  }

  /**
   * Должен возвращать строку вида: INSERT INTO таблица (поле[, поле]*) VALUES (значение[, значение]) - если установлены константные данные.
   * @covers PPHP\tools\patterns\database\query\Insert::interpretation
   */
  public function testShouldInterpretationIfConstValues(){
    $o = new Insert(new Table('table'));
    $o->addData(new Field('fieldA'), "1");
    $o->addData(new Field('fieldB'), "2");
    $o->addData(new Field('fieldC'), "3");
    $this->assertEquals('INSERT INTO `table` (`fieldA`,`fieldB`,`fieldC`) VALUES ("1","2","3")', $o->interpretation());
  }

  /**
   * Должен возвращать строку вида: INSERT INTO таблица инструкцияSelect - если установлен вложеный запрос.
   * @covers PPHP\tools\patterns\database\query\Insert::interpretation
   */
  public function testShouldInterpretationIfInsertedSelect(){
    $s = new Select;
    $s->addAllField();
    $s->addTable(new Table('people'));
    $o = new Insert(new Table('table'));
    $o->setSelect($s);
    $this->assertEquals('INSERT INTO `table` SELECT * FROM `people`', $o->interpretation());
  }

  /**
   * Вложенный запрос имеет больший приоритет.
   * @covers PPHP\tools\patterns\database\query\Insert::interpretation
   */
  public function testInsertedSelectShouldBeFirst(){
    $o = new Insert(new Table('table'));
    $o->addData(new Field('fieldA'), "1");
    $s = new Select;
    $s->addAllField();
    $s->addTable(new Table('people'));
    $o->setSelect($s);
    $this->assertEquals('INSERT INTO `table` SELECT * FROM `people`', $o->interpretation());
  }

  /**
   * Должен выбрасывать исключение в случае отсутствия константных данных или вложенного запроса.
   * @covers PPHP\tools\patterns\database\query\Insert::interpretation
   */
  public function testShouldThrowExceptionIfNotValues(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException');
    $o = new Insert(new Table('table'));
    $o->interpretation();
  }

  /**
   * Должен восстанавливаться из строки вида: INSERT INTO таблица (поле[, поле]*) VALUES (значение[, значение]).
   * @covers PPHP\tools\patterns\database\query\Insert::reestablish
   */
  public function testShouldRestorableForString(){
    $o = Insert::reestablish('INSERT INTO `table` (`fieldA`, `fieldB`, `fieldC`) VALUES ("1", "2", "3")');
    $this->assertEquals('INSERT INTO `table` (`fieldA`,`fieldB`,`fieldC`) VALUES ("1","2","3")', $o->interpretation());
    $this->assertEquals('table', $o->getTable()->getTableName());
  }

  /**
   * Допустимой строкой является строка вида: INSERT INTO таблица (поле[, поле]*) VALUES (значение[, значение]).
   * @covers PPHP\tools\patterns\database\query\Insert::isReestablish
   */
  public function testGoodString(){
    $this->assertTrue(Insert::isReestablish('INSERT INTO `table` (`fieldA`) VALUES ("1")'));
    $this->assertTrue(Insert::isReestablish('INSERT INTO `table` (`fieldA`,`fieldB`, `fieldC`) VALUES ("1","2","3")'));
    $this->assertTrue(Insert::isReestablish('INSERT INTO `table` (table.fieldA, TABLE.fieldB) VALUES ("1","2")'));
    $this->assertTrue(Insert::isReestablish('INSERT INTO `table`
                                                          (`fieldA`,`fieldB`, `fieldC`)
                                                   VALUES ("1",     "2",      "3")'));
  }

  /**
   * Должен возвращать false при недопустимой структуре строки.
   * @covers PPHP\tools\patterns\database\query\Insert::isReestablish
   */
  public function testBedString(){
    $this->assertFalse(Insert::isReestablish('INSERT `table` (`fieldA`,`fieldB`, `fieldC`) VALUES ("1","2","3")'));
    $this->assertFalse(Insert::isReestablish('INTO `table` (`fieldA`,`fieldB`, `fieldC`) VALUES ("1","2","3")'));
    $this->assertFalse(Insert::isReestablish('INSERT INTO `table` (`fieldA`,`fieldB`, `fieldC`) ("1","2","3")'));
    $this->assertFalse(Insert::isReestablish('INSERT INTO `table` `fieldA`,`fieldB`, `fieldC` VALUES ("1","2","3")'));
    $this->assertFalse(Insert::isReestablish('INSERT INTO `table` (`fieldA`,`fieldB`, `fieldC`) VALUES "1","2","3"'));
    $this->assertFalse(Insert::isReestablish('INSERT INTO `table` (`fieldA`,`fieldB`) VALUES ("1" "2")'));
    $this->assertFalse(Insert::isReestablish('INSERT INTO `table` (`fieldA` `fieldB`) VALUES ("1","2")'));
  }
}
