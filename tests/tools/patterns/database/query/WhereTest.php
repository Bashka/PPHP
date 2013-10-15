<?php
namespace PPHP\tests\tools\patterns\database\query;

use PPHP\tools\patterns\database\query\Field;
use PPHP\tools\patterns\database\query\LogicOperation;
use PPHP\tools\patterns\database\query\Where;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class WhereTest extends \PHPUnit_Framework_TestCase{
  /**
   * Должен определять логическое выражение.
   * @covers \PPHP\tools\patterns\database\query\Where::__construct
   */
  public function testShouldSetCondition(){
    $c = new LogicOperation(new Field('fieldA'), '=', new Field('fieldB'));
    $w = new Where($c);
    $this->assertEquals($c, $w->getCondition());
  }

  /**
   * Должен возвращать строку вида: WHERE условие.
   * @covers \PPHP\tools\patterns\database\query\Where::interpretation
   */
  public function testShouldInterpretation(){
    $w = new Where(new LogicOperation(new Field('a'), '=', 'a'));
    $this->assertEquals('WHERE (`a` = "a")', $w->interpretation());
  }

  /**
   * Должен восстанавливаться из строки вида: WHERE условие.
   * @covers \PPHP\tools\patterns\database\query\Where::reestablish
   */
  public function testShouldRestorableForString(){
    $r = Where::reestablish('WHERE (`field` = "0")');
    /**
     * @var \PPHP\tools\patterns\database\query\LogicOperation $c
     */
    $c = $r->getCondition();
    $this->assertEquals('0', $c->getValue());
  }

  /**
   * Допустимой строкой является строка вида: WHERE условие.
   * @covers \PPHP\tools\patterns\database\query\Where::isReestablish
   */
  public function testGoodString(){
    $this->assertTrue(Where::isReestablish('WHERE (`field` = "0")'));
    $this->assertTrue(Where::isReestablish('WHERE ((`fieldA` = "0")
                                                         AND (`fieldB` = "0"))'));
  }

  /**
   * Должен возвращать false при недопустимой структуре строки.
   * @covers \PPHP\tools\patterns\database\query\Where::isReestablish
   */
  public function testBedString(){
    $this->assertTrue(Where::isReestablish('WHERE (table.field = "0")'));
    $this->assertFalse(Where::isReestablish('(`field` = "0")'));
    $this->assertFalse(Where::isReestablish('WHERE `field` = "0"'));
    $this->assertFalse(Where::isReestablish('WHERE ()'));
  }
}
