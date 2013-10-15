<?php
namespace PPHP\tests\tools\patterns\database\query;

use PPHP\tools\patterns\database\query\Delete;
use PPHP\tools\patterns\database\query\Field;
use PPHP\tools\patterns\database\query\LogicOperation;
use PPHP\tools\patterns\database\query\Table;
use PPHP\tools\patterns\database\query\Where;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class DeleteTest extends \PHPUnit_Framework_TestCase{
  /**
   * Должен определять целевую таблицу.
   * @covers PPHP\tools\patterns\database\query\Delete::__construct
   */
  public function testShouldSetTable(){
    $o = new Delete(new Table('table'));
    $this->assertEquals('table', $o->getTable()->getTableName());
  }

  /**
   * Должен определять условие отбора.
   * @covers PPHP\tools\patterns\database\query\Delete::insertWhere
   */
  public function testShouldSetWhere(){
    $o = new Delete(new Table('table'));
    $w = new Where(new LogicOperation(new Field('field'), '=', '1'));
    $o->insertWhere($w);
    $this->assertEquals($w, $o->getWhere());
  }

  /**
   * Должен возвращать строку вида: DELETE FROM таблица[ WHERE условие].
   * @covers PPHP\tools\patterns\database\query\Delete::interpretation
   */
  public function testShouldInterpretation(){
    $o = new Delete(new Table('table'));
    $this->assertEquals('DELETE FROM `table`', $o->interpretation());
    $o->insertWhere(new Where(new LogicOperation(new Field('field'), '=', '1')));
    $this->assertEquals('DELETE FROM `table` WHERE (`field` = "1")', $o->interpretation());
  }

  /**
   * Должен восстанавливаться из строки вида: DELETE FROM таблица[ WHERE условие].
   * @covers PPHP\tools\patterns\database\query\Delete::reestablish
   */
  public function testShouldRestorableForString(){
    $d = Delete::reestablish('DELETE FROM `table` WHERE ((`field` >= "1") AND (`field` < "10"))');
    $this->assertEquals('table', $d->getTable()->getTableName());
    /**
     * @var \PPHP\tools\patterns\database\query\MultiCondition $c
     */
    $c = $d->getWhere()->getCondition();
    /**
     * @var \PPHP\tools\patterns\database\query\LogicOperation $o
     */
    $o = $c->getLeftOperand();
    $this->assertEquals('1', $o->getValue());
  }

  /**
   * Допустимой строкой является строка вида: DELETE FROM таблица[ WHERE условие].
   * @covers PPHP\tools\patterns\database\query\Delete::isReestablish
   */
  public function testGoodString(){
    $this->assertTrue(Delete::isReestablish('DELETE FROM `table` WHERE ((`field` >= "1") AND (`field` < "10"))'));
    $this->assertTrue(Delete::isReestablish('DELETE FROM `table`'));
    $this->assertTrue(Delete::isReestablish('DELETE FROM `table`
                                                   WHERE (`field` >= "1")'));
  }

  /**
   * Должен возвращать false при недопустимой структуре строки.
   * @covers PPHP\tools\patterns\database\query\Delete::isReestablish
   */
  public function testBedString(){
    $this->assertFalse(Delete::isReestablish('DELETE `table` WHERE (`field` >= "1")'));
    $this->assertFalse(Delete::isReestablish('FROM `table` WHERE (`field` >= "1")'));
    $this->assertFalse(Delete::isReestablish('DELETE FROM `table` (`field` >= "1")'));
  }
}
