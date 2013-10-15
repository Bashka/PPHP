<?php
namespace PPHP\tests\tools\patterns\database\query\builder;

use PPHP\tools\patterns\database\query\builder\Update;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class UpdateTest extends \PHPUnit_Framework_TestCase{
  protected function setUp(){
    Update::getInstance()->clear();
  }

  /**
   * Должен формировать объектную SQL инструкцию Update для указанной таблицы.
   * @covers \PPHP\tools\patterns\database\query\builder\Update::table
   */
  public function testShouldCreateObject(){
    $o = Update::getInstance()->table('people')->get();
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\Update', $o);
    $this->assertEquals('people', $o->getTable()->getTableName());
  }

  /**
   * Должен возвращать сформированную объектную SQL инструкцию PPHP\tools\patterns\database\query\Update.
   * @covers \PPHP\tools\patterns\database\query\builder\Update::get
   */
  public function testShouldReturnCreateObject(){
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\Update', Update::getInstance()->table('people')->get());
  }

  /**
   * Должен добавлять данные в инструкцию Update.
   * @covers \PPHP\tools\patterns\database\query\builder\Update::data
   */
  public function testShouldAddDataInObjectInsert(){
    $this->assertEquals(['petr'], Update::getInstance()->table('people')->data(['name' => 'petr'])->get()->getValues());
  }

  /**
   * Должен возвращать SQL инструкцию Update в виде строки.
   * @covers \PPHP\tools\patterns\database\query\builder\Update::interpretation
   */
  public function testShouldReturnSQLString(){
    $this->assertEquals('UPDATE `people` SET `name` = "petr"', Update::getInstance()->table('people')->data(['name' => 'petr'])->interpretation('mysql'));
    $this->assertEquals('UPDATE `people` SET `name` = "petr" WHERE (`name` = "ivan")', Update::getInstance()->table('people')->data(['name' => 'petr'])->where('name', '=', 'ivan')->update->interpretation('mysql'));
  }

  /**
   * Должен возвращать объект класса \PPHP\tools\patterns\database\query\builder\Where с указанным условием.
   * @covers \PPHP\tools\patterns\database\query\builder\Update::where
   */
  public function testShouldReturnObjectWhere(){
    $o = Update::getInstance()->table('people')->data(['name' => 'petr'])->where('name', '=', 'ivan');
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\builder\Where', $o);
    $this->assertEquals('name', $o->last()->getCondition()->getField()->getName());
  }

  /**
   * Должен добавлять свойство update, ссылающееся на фабрику, объекту класса \PPHP\tools\patterns\database\query\builder\Where.
   * @covers \PPHP\tools\patterns\database\query\builder\Update::where
   */
  public function testShouldAddProperty(){
    $o = Update::getInstance()->table('people')->data(['name' => 'petr'])->where('name', '=', 'ivan');
    $this->assertTrue(isset($o->update));
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\builder\Update', $o->update);
  }

  /**
   * Должен выбрасывать исключение при вызове до метода table.
   * @covers \PPHP\tools\patterns\database\query\builder\Update::where
   */
  public function testShouldThrowExceptionIfCallBeforeTableMethod(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException');
    $o = Update::getInstance()->where('id', '>', '5');
  }
}
