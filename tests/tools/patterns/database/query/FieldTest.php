<?php
namespace PPHP\tests\tools\patterns\database\query;
use PPHP\tools\patterns\database\query\Field as Field;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use \PPHP\tools\patterns\database\query\Table as Table;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class FieldTest extends \PHPUnit_Framework_TestCase {
  /**
   * @covers Field::__construct
   */
  public function testConstruct(){
    $o = new Field('test');
    $this->assertEquals('test', $o->getName());
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new Field('');
    new Field(1);
    new Field(null);
  }

  /**
   * @covers Field::interpretation
   */
  public function testInterpretation(){
    $o = new Field('testField');
    $this->assertEquals('`testField`', $o->interpretation());
    $o->setTable(new Table('testTable'));
    $this->assertEquals('testTable.testField', $o->interpretation());
  }

  /**
   * @covers Field::reestablish
   */
  public function testReestablish(){
    $f = Field::reestablish('`test`');
    $this->assertEquals('test', $f->getName());

    $f = Field::reestablish('table.field');
    $this->assertEquals('field', $f->getName());
    $this->assertEquals('table', $f->getTable()->getTableName());
  }

  /**
   * @covers Field::isReestablish
   */
  public function testIsReestablish(){
    $this->assertTrue(Field::isReestablish('`field`'));
    $this->assertTrue(Field::isReestablish('table.field'));
    $this->assertTrue(Field::isReestablish('`field5_`'));
    $this->assertTrue(Field::isReestablish('`_field`'));

    $this->assertFalse(Field::isReestablish('1field'));
    $this->assertFalse(Field::isReestablish('field+'));
    $this->assertFalse(Field::isReestablish('tableA.tableB.field'));
    $this->assertFalse(Field::isReestablish('1table.field'));
  }
}
