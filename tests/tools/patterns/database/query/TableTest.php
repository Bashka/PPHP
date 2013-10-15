<?php
namespace PPHP\tests\tools\patterns\database\query;

use PPHP\tools\patterns\database\query\Table;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class TableTest extends \PHPUnit_Framework_TestCase{
  /**
   * Должен идентифицировать объект именем таблицы.
   * @covers PPHP\tools\patterns\database\query\Table::__construct
   */
  public function testShouldInitObjectTableName(){
    $o = new Table('people');
    $this->assertEquals('people', $o->getTableName());
  }

  /**
   * Именем таблицы может быть только строка.
   * @covers PPHP\tools\patterns\database\query\Table::__construct
   */
  public function testTableNameStringIs(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new Table(1);
  }

  /**
   * Должен возвращать имя таблицы.
   * @covers PPHP\tools\patterns\database\query\Table::getTableName
   */
  public function testShouldReturnTableName(){
    $o = new Table('people');
    $this->assertEquals('people', $o->getTableName());
  }

  /**
   * Должен возвращать строку вида: имяТаблицы.
   * @covers PPHP\tools\patterns\database\query\Table::interpretation
   */
  public function testShouldInterpretationTableName(){
    $o = new Table('people');
    $this->assertEquals('people', $o->interpretation());
  }

  /**
   * Должен восстанавливаться из строки вида: имяТаблицы.
   * @covers PPHP\tools\patterns\database\query\Table::reestablish
   */
  public function testShouldRestorableForString(){
    /**
     * @var \PPHP\tools\patterns\database\query\Table $o
     */
    $o = Table::reestablish('people');
    $this->assertEquals('people', $o->interpretation());
  }

  /**
   * Допустимой строкой является строка вида: имяТаблицы.
   * @covers PPHP\tools\patterns\database\query\Table::isReestablish
   */
  public function testGoodString(){
    $this->assertTrue(Table::isReestablish('table'));
    $this->assertTrue(Table::isReestablish('table5'));
    $this->assertTrue(Table::isReestablish('table_test'));
  }

  /**
   * Должен возвращать false при недопустимой структуре строки.
   * @covers PPHP\tools\patterns\database\query\Table::isReestablish
   */
  public function testBedString(){
    $this->assertFalse(Table::isReestablish('1table'));
    $this->assertFalse(Table::isReestablish('tab le'));
    $this->assertFalse(Table::isReestablish('test+'));
  }
}
