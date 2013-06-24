<?php
namespace PPHP\tests\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\database\query as query;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class TableTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers query\Table::__construct
   */
  public function testConstruct(){
    $t = new query\Table('test');
    $this->assertEquals('test', $t->getTableName());
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new query\Table('');
    new query\Table(1);
  }

  /**
   * @covers query\Table::interpretation
   */
  public function testInterpretation(){
    $t = new query\Table('test');
    $this->assertEquals('test', $t->interpretation());
  }

  /**
   * @covers query\Table::isReestablish
   */
  public function testIsReestablish(){
    $this->assertTrue(query\Table::isReestablish('table'));
    $this->assertTrue(query\Table::isReestablish('table5'));
    $this->assertTrue(query\Table::isReestablish('table_test'));
    $this->assertTrue(query\Table::isReestablish('_table'));
    $this->assertFalse(query\Table::isReestablish('1table'));
    $this->assertFalse(query\Table::isReestablish('test+'));
  }

  /**
   * @covers query\Table::reestablish
   */
  public function testReestablish(){
    $t = query\Table::reestablish('test');
    $this->assertEquals('test', $t->getTableName());
  }
}
