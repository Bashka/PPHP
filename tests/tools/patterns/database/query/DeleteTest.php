<?php
namespace PPHP\tests\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use \PPHP\tools\patterns\database\query as query;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class DeleteTest extends \PHPUnit_Framework_TestCase {
  /**
   * @covers query\Delete::__construct
   */
  public function testConstruct(){
    $o = new query\Delete(new query\Table('table'));
    $this->assertEquals('table', $o->getTable()->getTableName());
  }

  /**
   * @covers query\Delete::insertWhere
   */
  public function testInsertWhere(){
    $o = new query\Delete(new query\Table('table'));
    $w = new query\Where(new query\LogicOperation(new query\Field('field'), '=', '1'));
    $o->insertWhere($w);
    $this->assertEquals($w, $o->getWhere());
  }

  /**
   * @covers query\Delete::interpretation
   */
  public function testInterpretation(){
    $o = new query\Delete(new query\Table('table'));
    $this->assertEquals('DELETE FROM `table`', $o->interpretation());
    $o->insertWhere(new query\Where(new query\LogicOperation(new query\Field('field'), '=', '1')));
    $this->assertEquals('DELETE FROM `table` WHERE (`field` = "1")', $o->interpretation());
  }

  /**
   * @covers query\Delete::isReestablish
   */
  public function testIsReestablish(){
    $this->assertTrue(query\Delete::isReestablish('DELETE FROM `table` WHERE ((`field` >= "1") AND (`field` < "10"))'));
    $this->assertTrue(query\Delete::isReestablish('DELETE FROM `table`'));
    $this->assertTrue(query\Delete::isReestablish('DELETE FROM `table`
                                                   WHERE (`field` >= "1")'));

    $this->assertFalse(query\Delete::isReestablish('DELETE `table` WHERE (`field` >= "1")'));
    $this->assertFalse(query\Delete::isReestablish('FROM `table` WHERE (`field` >= "1")'));
    $this->assertFalse(query\Delete::isReestablish('DELETE FROM `table` (`field` >= "1")'));
  }

  /**
   * @covers query\Delete::reestablish
   */
  public function testReestablish(){
    $d = query\Delete::reestablish('DELETE FROM `table` WHERE ((`field` >= "1") AND (`field` < "10"))');
    $this->assertEquals('table', $d->getTable()->getTableName());
    $this->assertEquals('1', $d->getWhere()->getCondition()->getLeftOperand()->getValue());
  }
}
