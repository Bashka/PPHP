<?php
namespace PPHP\tests\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\database\query as query;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class OrderByTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers query\OrderBy::__construct
   */
  public function testConstruct(){
    $ob = new query\OrderBy();
    $this->assertEquals('ASC', $ob->getSortedType());
    $ob = new query\OrderBy('ASC');
    $this->assertEquals('ASC', $ob->getSortedType());
    $ob = new query\OrderBy('DESC');
    $this->assertEquals('DESC', $ob->getSortedType());
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new query\OrderBy('TEST');
  }

  /**
   * @covers query\OrderBy::interpretation
   */
  public function testInterpretation(){
    $ob = new query\OrderBy();
    $ob->addField(new query\Field('testA'));
    $ob->addField(new query\Field('testB'));
    $this->assertEquals('ORDER BY `testA`,`testB` ASC', $ob->interpretation());
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException');
    $ob = new query\OrderBy();
    $ob->interpretation();
  }

  /**
   * @covers query\OrderBy::reestablish
   */
  public function testReestablish(){
    $ob = query\OrderBy::reestablish('ORDER BY `fieldA`,`fieldB`, `fieldC` DESC');
    $fields = $ob->getFields();
    $this->assertEquals('fieldA', $fields[0]->getName());
    $this->assertEquals('fieldC', $fields[2]->getName());
    $this->assertEquals('DESC', $ob->getSortedType());
    $ob = query\OrderBy::reestablish('ORDER BY table.fieldA, table.fieldB DESC');
    $fields = $ob->getFields();
    $this->assertEquals('table', $fields[0]->getTable()->getTableName());
  }

  /**
   * @covers query\OrderBy::isReestablish
   */
  public function testIsReestablish(){
    $this->assertTrue(query\OrderBy::isReestablish('ORDER BY `fieldA`,`fieldB`, table.fieldC DESC'));
    $this->assertTrue(query\OrderBy::isReestablish('ORDER BY `fieldA`,`fieldB`, table.fieldC ASC'));
    $this->assertFalse(query\OrderBy::isReestablish('ORDER `fieldA`,`fieldB`, table.fieldC DESC'));
    $this->assertFalse(query\OrderBy::isReestablish('ORDER BY `fieldA`,`fieldB`, table.fieldC'));
    $this->assertFalse(query\OrderBy::isReestablish('ORDER BY `fieldA`,`fieldB`, table.fieldC DSC'));
    $this->assertFalse(query\OrderBy::isReestablish('ORDER BY `fieldA`,`fieldB`, table.fieldC AC'));
  }
}
