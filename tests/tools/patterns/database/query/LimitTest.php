<?php
namespace PPHP\tests\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\database\query as query;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class LimitTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers query\Limit::__construct
   */
  public function testConstruct(){
    $l = new query\Limit(5);
    $this->assertEquals(5, $l->getCountRow());
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new query\Limit(0);
    new query\Limit('5');
  }

  /**
   * @covers query\Limit::interpretation
   */
  public function testInterpretation(){
    $l = new query\Limit(5);
    $this->assertEquals('TOP 5', $l->interpretation('sqlsrv'));
    $this->assertEquals('FIRST 5', $l->interpretation('firebird'));
    $this->assertEquals('ROWNUM <= 5', $l->interpretation('oci'));
    $this->assertEquals('LIMIT 5', $l->interpretation('mysql'));
    $this->assertEquals('LIMIT 5', $l->interpretation('pgsql'));
    $this->assertEquals('FETCH FIRST 5 ROWS ONLY', $l->interpretation('ibm'));
  }

  /**
   * @covers query\Limit::reestablish
   */
  public function testReestablish(){
    $l = query\Limit::reestablish('LIMIT 10');
    $this->assertEquals(10, $l->getCountRow());
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\StructureException');
    query\Limit::reestablish('10');
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    query\OrderBy::reestablish('LIMIT 0');
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    query\OrderBy::reestablish('LIMIT ');
  }
}
