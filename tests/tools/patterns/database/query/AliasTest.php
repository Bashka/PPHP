<?php
namespace PPHP\tests\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\database\query as query;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class AliasTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers query\Alias::__construct
   */
  public function testConstruct(){
    $o = new query\Field('test');
    $a = new query\FieldAlias($o, 'alias');
    $this->assertEquals($o, $a->getComponent());
    $this->assertEquals('alias', $a->getAlias());
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new query\FieldAlias($o, '');
    new query\FieldAlias($o, 1);
    new query\FieldAlias($o, null);
  }

  /**
   * @covers query\Alias::interpretation
   */
  public function testInterpretation(){
    $o = new query\Field('test');
    $a = new query\FieldAlias($o, 'alias');
    $this->assertEquals('`test` as alias', $a->interpretation());
  }

  /**
   * @covers query\Alias::reestablish
   * @covers query\FieldAlias::reestablish
   * @covers query\FieldAlias::reestablishChild
   */
  public function testReestablish(){
    $a = query\FieldAlias::reestablish('`test` as t');
    $this->assertEquals('test', $a->getComponent()->getName());
    $this->assertEquals('t', $a->getAlias());
    $a = query\FieldAlias::reestablish('table.field as t');
    $this->assertEquals('field', $a->getComponent()->getName());
    $this->assertEquals('table', $a->getComponent()->getTable()->getTableName());
    $this->assertEquals('t', $a->getAlias());
  }
}
