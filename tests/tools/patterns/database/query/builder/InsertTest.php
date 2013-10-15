<?php
namespace PPHP\tests\tools\patterns\database\query\builder;

use PPHP\tools\patterns\database\query\builder\Insert;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class InsertTest extends \PHPUnit_Framework_TestCase{
  /**
   * Должен формировать объектную SQL инструкцию Insert для указанной таблицы.
   * @covers \PPHP\tools\patterns\database\query\builder\Insert::table
   */
  public function testShouldCreateObject(){
    $o = Insert::getInstance()->table('people')->get();
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\Insert', $o);
    $this->assertEquals('people', $o->getTable()->getTableName());
  }

  /**
   * Должен возвращать сформированную объектную SQL инструкцию PPHP\tools\patterns\database\query\Insert.
   * @covers \PPHP\tools\patterns\database\query\builder\Insert::get
   */
  public function testShouldReturnCreateObject(){
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\Insert', Insert::getInstance()->table('people')->get());
  }

  /**
   * Должен добавлять данные в инструкцию Insert.
   * @covers \PPHP\tools\patterns\database\query\builder\Insert::data
   */
  public function testShouldAddDataInObjectInsert(){
    $this->assertEquals(['1', 'ivan', '12345'], Insert::getInstance()->table('people')->data(['OID' => '1', 'name' => 'ivan', 'phone' => '12345'])->get()->getValues());
  }

  /**
   * Должен возвращать SQL инструкцию Insert в виде строки.
   * @covers \PPHP\tools\patterns\database\query\builder\Insert::interpretation
   */
  public function testShouldReturnSQLString(){
    $this->assertEquals('INSERT INTO `people` (`OID`,`name`,`phone`) VALUES ("1","ivan","12345")', Insert::getInstance()->table('people')->data(['OID' => '1', 'name' => 'ivan', 'phone' => '12345'])->interpretation('mysql'));
  }
}
