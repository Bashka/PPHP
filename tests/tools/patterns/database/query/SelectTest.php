<?php
namespace PPHP\tests\tools\patterns\database\query;

use PPHP\tools\patterns\database\query\Field;
use PPHP\tools\patterns\database\query\FieldAlias;
use PPHP\tools\patterns\database\query\Join;
use PPHP\tools\patterns\database\query\Limit;
use PPHP\tools\patterns\database\query\LogicOperation;
use PPHP\tools\patterns\database\query\OrderBy;
use PPHP\tools\patterns\database\query\Select;
use PPHP\tools\patterns\database\query\Table;
use PPHP\tools\patterns\database\query\Where;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class SelectTest extends \PHPUnit_Framework_TestCase{
  /**
   * Должен добавлять в запрос поле.
   * @covers PPHP\tools\patterns\database\query\Select::addField
   */
  public function testShouldAddField(){
    $o = new Select;
    $f = new Field('f');
    $o->addField($f);
    $this->assertEquals($f, $o->getFields()[0]);
  }

  /**
   * Если указанное поле уже было добавлено, должен выбрасывать исключение.
   * @covers PPHP\tools\patterns\database\query\Select::addField
   */
  public function testShouldThrowExceptionIfFieldAdded(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $o = new Select;
    $f = new Field('f');
    $o->addField($f);
    $o->addField($f);
  }

  /**
   * Должен добавлять в запрос поле с псевдонимом.
   * @covers PPHP\tools\patterns\database\query\Select::addAliasField
   */
  public function testShouldAddAliasField(){
    $o = new Select;
    $f = new FieldAlias(new Field('f'), 'alias');
    $o->addAliasField($f);
    $this->assertEquals($f, $o->getFields()[0]);
  }

  /**
   * Если указанное поле с псевдонимом уже было добавлено, должен выбрасывать исключение.
   * @covers PPHP\tools\patterns\database\query\Select::addAliasField
   */
  public function testShouldThrowExceptionIfAliasFieldAdded(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $o = new Select;
    $f = new FieldAlias(new Field('f'), 'alias');
    $o->addAliasField($f);
    $o->addAliasField($f);
  }

  /**
   * Должен добавлять указанную таблицу в запрос.
   * @covers PPHP\tools\patterns\database\query\Select::addTable
   */
  public function testShouldAddTable(){
    $o = new Select;
    $t = new Table('table');
    $o->addTable($t);
    $this->assertEquals($t, $o->getTables()[0]);
  }

  /**
   * Если указанная таблица уже была добавлена, должен выбрасывать исключение.
   * @covers PPHP\tools\patterns\database\query\Select::addTable
   */
  public function testShouldThrowExceptionIfTableAdded(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $o = new Select;
    $t = new Table('table');
    $o->addTable($t);
    $o->addTable($t);
  }

  /**
   * Должен добавлять указанное объединение в запрос.
   * @covers PPHP\tools\patterns\database\query\Select::addJoin
   */
  public function testShouldAddJoin(){
    $o = new Select;
    $j = new Join(Join::INNER, new Table('table'), new LogicOperation(new Field('fieldA'), '=', new Field('fieldB')));
    $o->addJoin($j);
    $this->assertEquals($j, $o->getJoins()[0]);
  }

  /**
   * Если указанная таблица уже была добавлена, должен выбрасывать исключение.
   * @covers PPHP\tools\patterns\database\query\Select::addJoin
   */
  public function testShouldThrowExceptionIfJoinAdded(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $o = new Select;
    $j = new Join(Join::INNER, new Table('table'), new LogicOperation(new Field('fieldA'), '=', new Field('fieldB')));
    $o->addJoin($j);
    $o->addJoin($j);
  }

  /**
   * Должен устанавливать условие отбора.
   * @covers PPHP\tools\patterns\database\query\Select::insertWhere
   */
  public function testShouldSetWhere(){
    $o = new Select;
    $w = new Where(new LogicOperation(new Field('fieldA'), '=', new Field('fieldB')));
    $o->insertWhere($w);
    $this->assertEquals($w, $o->getWhere());
  }

  /**
   * Должен устанавливать схему сортировки.
   * @covers PPHP\tools\patterns\database\query\Select::insertOrderBy
   */
  public function testShouldSetOrderBy(){
    $o = new Select;
    $ob = new OrderBy();
    $ob->addField(new Field('fieldA'));
    $o->insertOrderBy($ob);
    $this->assertEquals($ob, $o->getOrderBy());
  }

  /**
   * Должен устанавливать порог запроса.
   * @covers PPHP\tools\patterns\database\query\Select::insertLimit
   */
  public function testShouldSetLimit(){
    $o = new Select;
    $l = new Limit(10);
    $o->insertLimit($l);
    $this->assertEquals($l, $o->getLimit());
  }

  /**
   * Должен определять, что запрашивают все поля таблиц.
   * @covers PPHP\tools\patterns\database\query\Select::addAllField
   */
  public function testShouldAddAllFields(){
    $o = new Select;
    $o->addAllField();
    $this->assertTrue($o->isAllFields());
  }

  /**
   * Должен возвращать строку вида: SELECT *|((имяПоля[ as псевдоним])|(имяТаблицы.имяПоля))+ FROM (имяТаблицы)+[ (объединение)+][ WHERE условиеОтбора][ ORDER BY схема][ LIMIT ограничение].
   * Результат может меняться в зависимости от требуемого SQL диалекта.
   * @covers PPHP\tools\patterns\database\query\Select::interpretation
   */
  public function testShouldInterpretation(){
    // Подготовка компонентов.
    $tA = new Table('tableA');
    $tB = new Table('tableB');
    $tC = new Table('tableC');
    $fA = new Field('fieldA');
    $fB = new Field('fieldB');
    $fC = new Field('fieldC');
    $fA->setTable($tA);
    $fC->setTable($tC);
    $fA = new FieldAlias($fA, 'fieldAAlias');
    $ob = new OrderBy;
    $ob->addField($fB);
    $w = new Where(new LogicOperation($fB, '>', "0"));
    $j = new Join(Join::INNER, $tC, new LogicOperation($fC, '=', $fB));
    // Сбор Select инструкции.
    $o = new Select;
    $o->addTable($tA);
    $o->addTable($tB);
    $o->addAliasField($fA);
    $o->addField($fB);
    $o->addJoin($j);
    $o->insertOrderBy($ob);
    $o->insertWhere($w);
    $this->assertEquals('SELECT tableA.fieldA as fieldAAlias,`fieldB` FROM `tableA`,`tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) WHERE (`fieldB` > "0") ORDER BY `fieldB` ASC', $o->interpretation());
  }

  /**
   * Если в инструкции присутствует компонент Limit, то в качестве параметра должен быть передан требуемый SQL диалект.
   * @covers PPHP\tools\patterns\database\query\Select::interpretation
   */
  public function testArgShouldBeStringIfLimitAdded(){
    $o = new Select;
    $o->addAllField();
    $o->addTable(new Table('tableA'));
    $o->insertLimit(new Limit(10));
    $o->interpretation('mysql');
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    $o->interpretation();
  }

  /**
   * Должен восстанавливаться из строки вида: SELECT *|((имяПоля[ as псевдоним])|(имяТаблицы.имяПоля))+ FROM (имяТаблицы)+[ (объединение)+][ ORDER BY схема][ LIMIT ограничение][ WHERE условиеОтбора].
   * @covers PPHP\tools\patterns\database\query\Select::reestablish
   */
  public function testShouldRestorableForString(){
    $o = Select::reestablish('SELECT * FROM `tableA`');
    $this->assertEquals([], $o->getFields());
    $o = Select::reestablish('SELECT `fieldA` FROM `tableA`');
    $this->assertEquals('fieldA', $o->getFields()[0]->getName());
    $this->assertEquals('tableA', $o->getTables()[0]->getTableName());
    $o = Select::reestablish('SELECT `fieldA` as fa FROM `tableA`');
    /**
     * @var \PPHP\tools\patterns\database\query\FieldAlias $a
     */
    $a = $o->getFields()[0];
    /**
     * @var \PPHP\tools\patterns\database\query\Field $f
     */
    $f = $a->getComponent();
    $this->assertEquals('fieldA', $f->getName());
    $this->assertEquals('fa', $o->getFields()[0]->getAlias());
    $o = Select::reestablish('SELECT `fieldA`, tableB.fieldB FROM `tableA`, `tableB`');
    $this->assertEquals('tableB', $o->getFields()[1]->getTable()->getTableName());
    $this->assertEquals('tableB', $o->getTables()[1]->getTableName());
    $o = Select::reestablish('SELECT `fieldA`, tableB.fieldB FROM `tableA`, `tableB` LIMIT 10');
    $this->assertEquals(10, $o->getLimit()->getCountRow());
    $o = Select::reestablish('SELECT `fieldA`, tableB.fieldB FROM `tableA`, `tableB` ORDER BY `fieldA`,tableB.fieldB DESC LIMIT 10');
    $this->assertEquals('DESC', $o->getOrderBy()->getSortedType());
    $this->assertEquals('fieldA', $o->getOrderBy()->getFields()[0]->getName());
    $o = Select::reestablish('SELECT `fieldA`, tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) ORDER BY `fieldA`,tableB.fieldB DESC LIMIT 10');
    $this->assertEquals('INNER', $o->getJoins()[0]->getType());
    $o = Select::reestablish('SELECT `fieldA`, tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) LEFT JOIN `tableD` ON (tableD.id = `fieldB`) ORDER BY `fieldA`,tableB.fieldB DESC LIMIT 10');
    $this->assertEquals('tableD', $o->getJoins()[1]->getTable()->getTableName());
    $o = Select::reestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) LEFT JOIN `tableD` ON (tableD.id = `fieldB`) ORDER BY `fieldA`,tableB.fieldB ASC LIMIT 10 WHERE ((`fieldB` > "0") OR (tableA.fieldA < "10"))');
    /**
     * @var \PPHP\tools\patterns\database\query\MultiCondition $m
     */
    $m = $o->getWhere()->getCondition();
    /**
     * @var \PPHP\tools\patterns\database\query\LogicOperation $l
     */
    $l = $m->getLeftOperand();
    $this->assertEquals('0', $l->getValue());
    $this->assertEquals('SELECT `fieldA`,tableB.fieldB FROM `tableA`,`tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) LEFT JOIN `tableD` ON (tableD.id = `fieldB`) WHERE ((`fieldB` > "0") OR (tableA.fieldA < "10")) ORDER BY `fieldA`,tableB.fieldB ASC LIMIT 10', $o->interpretation('mysql'));
  }

  /**
   * Допустимой строкой является строка вида: SELECT *|((имяПоля[ as псевдоним])|(имяТаблицы.имяПоля))+ FROM (имяТаблицы)+[ (объединение)+][ ORDER BY схема][ LIMIT ограничение][ WHERE условиеОтбора].
   * @covers PPHP\tools\patterns\database\query\Select::isReestablish
   */
  public function testGoodString(){
    $this->assertTrue(Select::isReestablish('SELECT * FROM `tableA`'));
    $this->assertTrue(Select::isReestablish('SELECT `fieldA` FROM `tableA`'));
    $this->assertTrue(Select::isReestablish('SELECT `fieldA` as fa FROM `tableA`'));
    $this->assertTrue(Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB`'));
    $this->assertTrue(Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`)'));
    $this->assertTrue(Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) LEFT JOIN `tableD` ON (tableD.id = `fieldB`)'));
    $this->assertTrue(Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) WHERE ((`fieldB` > "0") OR (tableA.fieldA < "10"))'));
    $this->assertTrue(Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) ORDER BY `fieldA`,tableB.fieldB ASC WHERE ((`fieldB` > "0") OR (tableA.fieldA < "10"))'));
    $this->assertTrue(Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) ORDER BY `fieldA`,tableB.fieldB DESC LIMIT 10 WHERE ((`fieldB` > "0") OR (tableA.fieldA < "10"))'));
  }

  /**
   * Должен возвращать false при недопустимой структуре строки.
   * @covers PPHP\tools\patterns\database\query\Select::isReestablish
   */
  public function testBedString(){
    $this->assertFalse(Select::isReestablish('`fieldA` FROM `tableA`'));
    $this->assertFalse(Select::isReestablish('SELECT `fieldA` `tableA`'));
    $this->assertFalse(Select::isReestablish('SELECT`fieldA` FROM `tableA`'));
    $this->assertFalse(Select::isReestablish('SELECT `fieldA` FROM`tableA`'));
    $this->assertFalse(Select::isReestablish('SELECT `fieldA` FROM tableA'));
    $this->assertFalse(Select::isReestablish('SELECT fieldA FROM `tableA`'));
    $this->assertFalse(Select::isReestablish('SELECT `fieldA` tableB.fieldB FROM `tableA`, `tableB`'));
    $this->assertFalse(Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA` `tableB`'));
    $this->assertFalse(Select::isReestablish('SELECT FROM `tableA`, `tableB`'));
    $this->assertFalse(Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM '));
    $this->assertFalse(Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB`INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`)'));
    $this->assertFalse(Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC`ON(tableC.fieldC = `fieldB`)'));
    $this->assertFalse(Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` JOIN `tableC` ON (tableC.fieldC = `fieldB`)'));
    $this->assertFalse(Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON ()'));
    $this->assertFalse(Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`), LEFT JOIN `tableD` ON(tableD.id = `fieldB`)'));
    $this->assertFalse(Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) WHERE((`fieldB` > "0") OR (tableA.fieldA < "10"))'));
    $this->assertFalse(Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) ((`fieldB` > "0") OR (tableA.fieldA < "10"))'));
    $this->assertFalse(Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) WHERE ()'));
    $this->assertFalse(Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) ORDER BY `fieldA`,tableB.fieldB WHERE ((`fieldB` > "0") OR (tableA.fieldA < "10"))'));
    $this->assertFalse(Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) ORDER BY `fieldA` tableB.fieldB WHERE ((`fieldB` > "0") OR (tableA.fieldA < "10"))'));
    $this->assertFalse(Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) ORDER BY `fieldA`,tableB.fieldB LIMIT 0 WHERE ((`fieldB` > "0") OR (tableA.fieldA < "10"))'));
  }
}
