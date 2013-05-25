<?php
namespace PPHP\tests\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use \PPHP\tools\patterns\database\query as query;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class FieldAliasTest extends \PHPUnit_Framework_TestCase {
  /**
   * @covers FieldAlias::__construct
   */
  public function testConstruct(){
    $o = new query\Field('test');
    new query\FieldAlias($o, 'alias');

    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new query\FieldAlias('test', 'alias');
    new query\FieldAlias(1, 'alias');
    new query\FieldAlias(null, 'alias');
  }

  /**
   * @covers query\FieldAlias::isReestablish
   */
  public function testIsReestablish(){
    $this->assertTrue(query\FieldAlias::isReestablish('`test` as t'));
    $this->assertTrue(query\FieldAlias::isReestablish('table.field as f'));
    $this->assertTrue(query\FieldAlias::isReestablish('`field5_` as f'));
    $this->assertTrue(query\FieldAlias::isReestablish('`_field` as f'));

    $this->assertFalse(query\FieldAlias::isReestablish('`1field` as f'));
    $this->assertFalse(query\FieldAlias::isReestablish('`field` f'));
    $this->assertFalse(query\FieldAlias::isReestablish('tableA.field as '));
    $this->assertFalse(query\FieldAlias::isReestablish('table.field as 5'));
  }
}
