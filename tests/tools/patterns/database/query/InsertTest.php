<?php
namespace PPHP\tests\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use \PPHP\tools\patterns\database\query as query;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class InsertTest extends \PHPUnit_Framework_TestCase {
  /**
   * @covers query\Insert::__construct
   */
  public function testConstruct(){
    $o = new query\Insert(new query\Table('table'));
    $this->assertEquals('table', $o->getTable()->getTableName());
  }

  /**
   * @covers query\Insert::interpretation
   */
  public function testInterpretation(){
    $o = new query\Insert(new query\Table('table'));
    $o->addData(new query\Field('fieldA'), "1");
    $o->addData(new query\Field('fieldB'), "2");
    $o->addData(new query\Field('fieldC'), "3");
    $this->assertEquals('INSERT INTO `table` (`fieldA`,`fieldB`,`fieldC`) VALUES ("1","2","3")', $o->interpretation());

    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException');
    $o = new query\Insert(new query\Table('table'));
    $o->interpretation();
  }

  /**
   * @covers query\Insert::isReestablish
   */
  public function testIsReestablish(){
    $this->assertTrue(query\Insert::isReestablish('INSERT INTO `table` (`fieldA`) VALUES ("1")'));
    $this->assertTrue(query\Insert::isReestablish('INSERT INTO `table` (`fieldA`,`fieldB`, `fieldC`) VALUES ("1","2","3")'));
    $this->assertTrue(query\Insert::isReestablish('INSERT INTO `table` (table.fieldA, table.fieldB) VALUES ("1","2")'));
    $this->assertTrue(query\Insert::isReestablish('INSERT INTO `table`
                                                          (`fieldA`,`fieldB`, `fieldC`)
                                                   VALUES ("1",     "2",      "3")'));

    $this->assertFalse(query\Insert::isReestablish('INSERT `table` (`fieldA`,`fieldB`, `fieldC`) VALUES ("1","2","3")'));
    $this->assertFalse(query\Insert::isReestablish('INTO `table` (`fieldA`,`fieldB`, `fieldC`) VALUES ("1","2","3")'));
    $this->assertFalse(query\Insert::isReestablish('INSERT INTO `table` (`fieldA`,`fieldB`, `fieldC`) ("1","2","3")'));
    $this->assertFalse(query\Insert::isReestablish('INSERT INTO `table` `fieldA`,`fieldB`, `fieldC` VALUES ("1","2","3")'));
    $this->assertFalse(query\Insert::isReestablish('INSERT INTO `table` (`fieldA`,`fieldB`, `fieldC`) VALUES "1","2","3"'));
    $this->assertFalse(query\Insert::isReestablish('INSERT INTO `table` (`fieldA`,`fieldB`) VALUES ("1" "2")'));
    $this->assertFalse(query\Insert::isReestablish('INSERT INTO `table` (`fieldA` `fieldB`) VALUES ("1","2")'));
  }

  /**
   * @covers query\INLogicOperation::reestablish
   */
  public function testReestablish(){
    $o = query\Insert::reestablish('INSERT INTO `table` (`fieldA`, `fieldB`, `fieldC`) VALUES ("1", "2", "3")');
    $this->assertEquals('INSERT INTO `table` (`fieldA`,`fieldB`,`fieldC`) VALUES ("1","2","3")', $o->interpretation());
    $this->assertEquals('table', $o->getTable()->getTableName());
  }
}
