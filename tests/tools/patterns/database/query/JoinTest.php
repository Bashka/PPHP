<?php
namespace PPHP\tests\tools\patterns\database\query;

use PPHP\tools\patterns\database\query\Field;
use PPHP\tools\patterns\database\query\Join;
use PPHP\tools\patterns\database\query\LogicOperation;
use PPHP\tools\patterns\database\query\Table;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class JoinTest extends \PHPUnit_Framework_TestCase{
  /**
   * Должен поределять тип, целевую таблицу и условие объединения.
   * @covers PPHP\tools\patterns\database\query\Join::__construct
   */
  public function testShouldSetJoinComponents(){
    $o = new Join(Join::INNER, new Table('table'), new LogicOperation(new Field('fieldA'), '=', new Field('fieldB')));
    $this->assertEquals(Join::INNER, $o->getType());
    $this->assertEquals('table', $o->getTable()->getTableName());
    /**
     * @var \PPHP\tools\patterns\database\query\LogicOperation $c
     */
    $c = $o->getCondition();
    $this->assertEquals('fieldB', $c->getValue()->getName());
  }

  /**
   * В качестве типа может быть только: CROSS, INNER, LEFT, RIGHT, FULL.
   * @covers PPHP\tools\patterns\database\query\Join::__construct
   */
  public function testTestShouldBeCROSSorINNERorLEFTorRIGHTorFULL(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new Join('TEST', new Table('table'), new LogicOperation(new Field('fieldA'), '=', new Field('fieldB')));
  }

  /**
   * Должен возвращать строку вида: тип JOIN `таблица` ON условие.
   * @covers PPHP\tools\patterns\database\query\Join::interpretation
   */
  public function testShouldInterpretation(){
    $o = new Join(Join::INNER, new Table('table'), new LogicOperation(new Field('fieldA'), '=', new Field('fieldB')));
    $this->assertEquals('INNER JOIN `table` ON (`fieldA` = `fieldB`)', $o->interpretation());
  }

  /**
   * Должен восстанавливаться из строки вида: тип JOIN `таблица` ON условие
   * @covers PPHP\tools\patterns\database\query\Join::reestablish
   */
  public function testShouldRestorableForString(){
    /**
     * @var \PPHP\tools\patterns\database\query\Join $j
     */
    $j = Join::reestablish('INNER JOIN `table` ON (`fieldA` = `fieldB`)');
    $this->assertEquals(Join::INNER, $j->getType());
    $this->assertEquals('table', $j->getTable()->getTableName());
    /**
     * @var \PPHP\tools\patterns\database\query\LogicOperation $c
     */
    $c = $j->getCondition();
    $this->assertEquals('fieldB', $c->getValue()->getName());
  }

  /**
   * Допустимой строкой является строка вида: тип JOIN `таблица` ON условие
   * @covers PPHP\tools\patterns\database\query\Join::isReestablish
   */
  public function testGoodString(){
    $this->assertTrue(Join::isReestablish('INNER JOIN `table` ON (`fieldA` = `fieldB`)'));
    $this->assertTrue(Join::isReestablish('LEFT JOIN `table` ON (table.fieldA = table.fieldB)'));
    $this->assertTrue(Join::isReestablish('LEFT JOIN `table`
                                                 ON (table.fieldA = table.fieldB)'));
  }

  /**
   * Должен возвращать false при недопустимой структуре строки.
   * @covers PPHP\tools\patterns\database\query\Join::isReestablish
   */
  public function testBedString(){
    $this->assertFalse(Join::isReestablish('X JOIN `table` ON (`fieldA` = `fieldB`)'));
    $this->assertFalse(Join::isReestablish('CROSI JOIN `table` ON (`fieldA` = `fieldB`)'));
    $this->assertFalse(Join::isReestablish('INNER J `table` ON (`fieldA` = `fieldB`)'));
    $this->assertFalse(Join::isReestablish('INNER JOIN `table` (`fieldA` = `fieldB`)'));
    $this->assertFalse(Join::isReestablish('INNER JOIN `table` ON `fieldA` = `fieldB`'));
  }
}
