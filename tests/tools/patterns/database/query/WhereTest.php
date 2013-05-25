<?php
namespace PPHP\tests\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use \PPHP\tools\patterns\database\query as query;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class WhereTest extends \PHPUnit_Framework_TestCase {
  /**
   * @covers query\Where::interpretation
   */
  public function testInterpretation(){
    $w = new query\Where(new query\LogicOperation(new query\Field('a'), '=', 'a'));
    $this->assertEquals('WHERE (`a` = "a")', $w->interpretation());
  }

  /**
   * @covers query\Where::isReestablish
   */
  public function testIsReestablish(){
    $this->assertTrue(query\Where::isReestablish('WHERE (`field` = "0")'));
    $this->assertTrue(query\Where::isReestablish('WHERE ((`fieldA` = "0")
                                                         AND (`fieldB` = "0"))'));
    $this->assertTrue(query\Where::isReestablish('WHERE (table.field = "0")'));

    $this->assertFalse(query\Where::isReestablish('(`field` = "0")'));
    $this->assertFalse(query\Where::isReestablish('WHERE `field` = "0"'));
    $this->assertFalse(query\Where::isReestablish('WHERE ()'));
  }

  /**
   * @covers query\Where::reestablish
   */
  public function testReestablish(){
    $r = query\Where::reestablish('WHERE (`field` = "0")');
    $this->assertEquals('0', $r->getCondition()->getValue());
  }
}
