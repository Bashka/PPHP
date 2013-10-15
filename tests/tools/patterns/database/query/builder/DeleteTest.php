<?php
namespace PPHP\tests\tools\patterns\database\query\builder;

use PPHP\tools\patterns\database\query\builder\Delete;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class DeleteTest extends \PHPUnit_Framework_TestCase{
  protected function setUp(){
    Delete::getInstance()->clear();
  }

  /**
   * Должен формировать объектную SQL инструкцию Delete для указанной таблицы.
   * @covers \PPHP\tools\patterns\database\query\builder\Delete::table
   */
  public function testShouldCreateObject(){
    $o = Delete::getInstance()->table('people')->get();
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\Delete', $o);
    $this->assertEquals('people', $o->getTable()->getTableName());
  }

  /**
   * Должен возвращать сформированную объектную SQL инструкцию PPHP\tools\patterns\database\query\Delete.
   * @covers \PPHP\tools\patterns\database\query\builder\Delete::get
   */
  public function testShouldReturnCreateObject(){
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\Delete', Delete::getInstance()->table('people')->get());
  }

  /**
   * Должен возвращать SQL инструкцию Delete в виде строки.
   * @covers \PPHP\tools\patterns\database\query\builder\Delete::interpretation
   */
  public function testShouldReturnSQLString(){
    $this->assertEquals('DELETE FROM `people`', Delete::getInstance()->table('people')->interpretation('mysql'));
    $this->assertEquals('DELETE FROM `people` WHERE (`id` > "5")', Delete::getInstance()->table('people')->where('id', '>', '5')->delete->interpretation('mysql'));
  }

  /**
   * Должен возвращать объект класса \PPHP\tools\patterns\database\query\builder\Where с указанным условием.
   * @covers \PPHP\tools\patterns\database\query\builder\Delete::where
   */
  public function testShouldReturnObjectWhere(){
    $o = Delete::getInstance()->table('people')->where('id', '>', '5');
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\builder\Where', $o);
    $this->assertEquals('id', $o->last()->getCondition()->getField()->getName());
  }

  /**
   * Должен добавлять свойство delete, ссылающееся на фабрику, объекту класса \PPHP\tools\patterns\database\query\builder\Where.
   * @covers \PPHP\tools\patterns\database\query\builder\Delete::where
   */
  public function testShouldAddProperty(){
    $o = Delete::getInstance()->table('people')->where('id', '>', '5');
    $this->assertTrue(isset($o->delete));
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\builder\Delete', $o->delete);
  }

  /**
   * Должен выбрасывать исключение при вызове до метода table.
   * @covers \PPHP\tools\patterns\database\query\builder\Delete::where
   */
  public function testShouldThrowExceptionIfCallBeforeTableMethod(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException');
    $o = Delete::getInstance()->where('id', '>', '5');
  }
}
