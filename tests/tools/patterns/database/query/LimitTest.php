<?php
namespace PPHP\tests\tools\patterns\database\query;

use PPHP\tools\patterns\database\query\Limit;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class LimitTest extends \PHPUnit_Framework_TestCase{
  /**
   * Должен определять количество строк.
   * @covers PPHP\tools\patterns\database\query\Limit::__construct
   */
  public function testShouldSetCountRows(){
    $l = new Limit(5);
    $this->assertEquals(5, $l->getCountRow());
  }

  /**
   * Должен быть целым числом большим нуля.
   * @covers PPHP\tools\patterns\database\query\Limit::__construct
   */
  public function testShouldBeInteger(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new Limit(0);
    new Limit('5');
  }

  /**
   * Должен возвращать число выбираемых строк.
   * @covers PPHP\tools\patterns\database\query\Limit::getCountRow
   */
  public function testShouldReturnCountRows(){
    $l = new Limit(5);
    $this->assertEquals(5, $l->getCountRow());
  }

  /**
   * Должен возвращать SQL компонент согласно выбранной СУБД.
   * @covers PPHP\tools\patterns\database\query\Limit::interpretation
   */
  public function testShouldReturnString(){
    $l = new Limit(5);
    $this->assertEquals('TOP 5', $l->interpretation('sqlsrv'));
    $this->assertEquals('FIRST 5', $l->interpretation('firebird'));
    $this->assertEquals('ROWNUM <= 5', $l->interpretation('oci'));
    $this->assertEquals('LIMIT 5', $l->interpretation('mysql'));
    $this->assertEquals('LIMIT 5', $l->interpretation('pgsql'));
    $this->assertEquals('FETCH FIRST 5 ROWS ONLY', $l->interpretation('ibm'));
  }

  /**
   * Должен восстанавливаться из строки вида: LIMIT числоСтрок.
   * @covers PPHP\tools\patterns\database\query\Limit::reestablish
   */
  public function testShouldRestorableForString(){
    /**
     * @var Limit $l
     */
    $l = Limit::reestablish('LIMIT 10');
    $this->assertEquals(10, $l->getCountRow());
  }

  /**
   * Допустимой строкой является строка вида: LIMIT числоСтрок.
   * @covers PPHP\tools\patterns\database\query\Limit::isReestablish
   */
  public function testGoodString(){
    $this->assertTrue(Limit::isReestablish('LIMIT 1'));
    $this->assertTrue(Limit::isReestablish('LIMIT 99'));
  }

  /**
   * Должен возвращать false при недопустимой структуре строки.
   * @covers PPHP\tools\patterns\database\query\Limit::isReestablish
   */
  public function testBedString(){
    $this->assertFalse(Limit::isReestablish('1'));
    $this->assertFalse(Limit::isReestablish('LIMIT'));
    $this->assertFalse(Limit::isReestablish('LIMIT 0'));
    $this->assertFalse(Limit::isReestablish('LIMIT -1'));
    $this->assertFalse(Limit::isReestablish('LIMIT 1a'));
  }
}
