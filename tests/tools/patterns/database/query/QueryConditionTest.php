<?php
namespace PPHP\tests\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use \PPHP\tools\patterns\database\query as query;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class QueryConditionTest extends \PHPUnit_Framework_TestCase {
  /**
   * @covers query\QueryCondition::interpretation
   */
  public function testInterpretation(){
    $cc = new query\AndMultiCondition;
    $cc->addCondition(new query\LogicOperation(new query\Field('a'), '=', 'a'));
    $cc->addCondition(new query\LogicOperation(new query\Field('b'), '=', 'b'));
    $cc->addCondition(new query\LogicOperation(new query\Field('c'), '=', 'c'));
    $this->assertEquals('((`a` = "a") AND (`b` = "b") AND (`c` = "c"))', $cc->interpretation());

    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException');
    $cc = new query\AndMultiCondition;
    $cc->addCondition(new query\LogicOperation(new query\Field('a'), '=', 'a'));
    $cc->interpretation();
  }

  /**
   * @covers query\QueryCondition::reestablish
   */
  public function testReestablish(){
    $o = query\AndMultiCondition::reestablish('((`a` = "a") AND (`b` = "b") AND (`c` = "c"))');
    $this->assertEquals('a', $o->getConditions()[0]->getValue());
    $this->assertEquals('=', $o->getConditions()[0]->getOperator());

    $o = query\OrMultiCondition::reestablish('((`a` = "a") OR (`b` = "b") OR (`c` = "c"))');
    $this->assertEquals('a', $o->getConditions()[0]->getValue());
    $this->assertEquals('=', $o->getConditions()[0]->getOperator());
  }

  /**
   * @covers query\QueryCondition::isReestablish
   */
  public function testIsReestablish(){
    $this->assertTrue(query\OrMultiCondition::isReestablish('((`a` = "a") OR (`b` = "b"))'));
    $this->assertTrue(query\AndMultiCondition::isReestablish('((`a` = "a") AND (`b` = "b") AND (`c` = "c"))'));
    $this->assertTrue(query\OrMultiCondition::isReestablish('((`a` = "a") OR (`b` = "b") OR (`c` = "c"))'));
    $this->assertTrue(query\OrMultiCondition::isReestablish('(
                                                                (
                                                                  (`a` = "a") AND
                                                                  (`b` = "b") AND
                                                                  (`c` = "c")
                                                                ) OR
                                                                (`b` = "b") OR
                                                                (`c` = "c")
                                                              )'));


    $this->assertFalse(query\AndMultiCondition::isReestablish('((`a` = "a") OR (`b` = "b"))'));
    $this->assertFalse(query\AndMultiCondition::isReestablish('((`a` = "a") (`b` = "b"))'));
    $this->assertFalse(query\AndMultiCondition::isReestablish('((`a` = "a"))'));
  }
}
