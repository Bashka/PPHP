<?php
namespace PPHP\tests\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use \PPHP\tools\patterns\database\query as query;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class JoinTest extends \PHPUnit_Framework_TestCase {
  /**
   * @covers query\Join::__construct
   */
  public function testConstruct(){
    $o = new query\Join(query\Join::INNER, new query\Table('table'), new query\LogicOperation(new query\Field('fieldA'), '=', new query\Field('fieldB')));
    $this->assertEquals(query\Join::INNER, $o->getType());
    $this->assertEquals('table', $o->getTable()->getTableName());
    $this->assertEquals('fieldB', $o->getCondition()->getValue()->getName());
  }

  /**
   * @covers query\Join::interpretation
   */
  public function testInterpretation(){
    $o = new query\Join(query\Join::INNER, new query\Table('table'), new query\LogicOperation(new query\Field('fieldA'), '=', new query\Field('fieldB')));
    $this->assertEquals('INNER JOIN `table` ON (`fieldA` = `fieldB`)', $o->interpretation());
  }

  /**
   * @covers query\Join::reestablish
   */
  public function testReestablish(){
    $j = query\Join::reestablish('INNER JOIN `table` ON (`fieldA` = `fieldB`)');
    $this->assertEquals(query\Join::INNER, $j->getType());
    $this->assertEquals('table', $j->getTable()->getTableName());
    $this->assertEquals('fieldB', $j->getCondition()->getValue()->getName());
  }

  /**
   * @covers query\Join::isReestablish
   */
  public function testIsReestablish(){
    $this->assertTrue(query\Join::isReestablish('INNER JOIN `table` ON (`fieldA` = `fieldB`)'));
    $this->assertTrue(query\Join::isReestablish('LEFT JOIN `table` ON (table.fieldA = table.fieldB)'));
    $this->assertTrue(query\Join::isReestablish('LEFT JOIN `table`
                                                 ON (table.fieldA = table.fieldB)'));

    $this->assertFalse(query\Join::isReestablish('X JOIN `table` ON (`fieldA` = `fieldB`)'));
    $this->assertFalse(query\Join::isReestablish('CROSI JOIN `table` ON (`fieldA` = `fieldB`)'));
    $this->assertFalse(query\Join::isReestablish('INNER J `table` ON (`fieldA` = `fieldB`)'));
    $this->assertFalse(query\Join::isReestablish('INNER JOIN `table` (`fieldA` = `fieldB`)'));
    $this->assertFalse(query\Join::isReestablish('INNER JOIN `table` ON `fieldA` = `fieldB`'));
  }
}
