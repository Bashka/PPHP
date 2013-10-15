<?php
namespace PPHP\tests\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\database\query as query;
use PPHP\tools\patterns\database\query\Field;
use PPHP\tools\patterns\database\query\FieldAlias;
use PPHP\tools\patterns\database\query\Table;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class FieldAliasTest extends \PHPUnit_Framework_TestCase{
  /**
   * Должен определять целевое поле и его псевданим.
   * @covers \PPHP\tools\patterns\database\query\FieldAlias::__construct
   */
  public function testShouldSetFieldAndAlias(){
    $o = new FieldAlias(new Field('name'), 'peopleName');
    $this->assertEquals('peopleName', $o->getAlias());
  }

  /**
   * В качестве псевдонима может выступать только не пустая строка.
   * @covers \PPHP\tools\patterns\database\query\Alias::__construct
   */
  public function testAliasCanBeString(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new FieldAlias(new Field('name'), 1);
  }

  /**
   * В качестве целевого поля может выступать только объект класса Field.
   * @covers \PPHP\tools\patterns\database\query\FieldAlias::__construct
   */
  public function testFieldCanBeObjectField(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new FieldAlias(new Table('people'), 'peopleName');
  }

  /**
   * Должен возвращать псевдоним.
   * @covers \PPHP\tools\patterns\database\query\Alias::getAlias
   */
  public function testShouldReturnAlias(){
    $o = new FieldAlias(new Field('name'), 'peopleName');
    $this->assertEquals('peopleName', $o->getAlias());
  }

  /**
   * Должен возвращать целевое поле.
   * @covers \PPHP\tools\patterns\database\query\FieldAlias::getComponent
   */
  public function testShouldReturnField(){
    $o = new FieldAlias(new Field('name'), 'peopleName');
    /**
     * @var \PPHP\tools\patterns\database\query\Field $c
     */
    $c = $o->getComponent();
    $this->assertEquals('name', $c->getName());
  }

  /**
   * Должен формировать строку вида: `имяПоля` as псевдоним.
   * @covers \PPHP\tools\patterns\database\query\FieldAlias::interpretation
   */
  public function testShouldInterpretationAlias(){
    $o = new FieldAlias(new Field('name'), 'peopleName');
    $this->assertEquals('`name` as peopleName', $o->interpretation());
  }

  /**
   * Должен восстанавливать объект из строки вида: `имяПоля` as псевдоним - и вида: имяТаблицы.имяПоля as псевдоним.
   * @covers \PPHP\tools\patterns\database\query\FieldAlias::reestablish
   */
  public function testShouldRestorableForString(){
    /**
     * @var \PPHP\tools\patterns\database\query\FieldAlias $o
     */
    $o = FieldAlias::reestablish('`name` as peopleName');
    $this->assertEquals('peopleName', $o->getAlias());
    /**
     * @var \PPHP\tools\patterns\database\query\Field $c
     */
    $c = $o->getComponent();
    $this->assertEquals('name', $c->getName());
    /**
     * @var \PPHP\tools\patterns\database\query\FieldAlias $o
     */
    $o = FieldAlias::reestablish('people.name as peopleName');
    /**
     * @var \PPHP\tools\patterns\database\query\Field $c
     */
    $c = $o->getComponent();
    $this->assertEquals('people', $c->getTable()->getTableName());
  }

  /**
   * Допустимыми являются строки вида: `имяПоля` as псевдоним - и вида: имяТаблицы.имяПоля as псевдоним.
   * @covers \PPHP\tools\patterns\database\query\FieldAlias::isReestablish
   */
  public function testGoodString(){
    $this->assertTrue(FieldAlias::isReestablish('`test` as t'));
    $this->assertTrue(FieldAlias::isReestablish('table.field as f'));
    $this->assertTrue(FieldAlias::isReestablish('`field5_` as f'));
    $this->assertTrue(FieldAlias::isReestablish('`_field` as f'));
  }

  /**
   * Должен
   * @covers \PPHP\tools\patterns\database\query\FieldAlias::isReestablish
   */
  public function testBedString(){
    $this->assertFalse(query\FieldAlias::isReestablish('`1field` as f'));
    $this->assertFalse(query\FieldAlias::isReestablish('`field` f'));
    $this->assertFalse(query\FieldAlias::isReestablish('tableA.field as '));
    $this->assertFalse(query\FieldAlias::isReestablish('table.field as 5'));
  }
}
