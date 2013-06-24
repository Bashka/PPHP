<?php
namespace PPHP\tests\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\database\query as query;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class SelectTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers query\Select::interpretation
   */
  public function testInterpretation(){
    // Подготовка компонентов
    $tA = new query\Table('tableA');
    $tB = new query\Table('tableB');
    $tC = new query\Table('tableC');
    $fA = new query\Field('fieldA');
    $fB = new query\Field('fieldB');
    $fC = new query\Field('fieldC');
    $fA->setTable($tA);
    $fC->setTable($tC);
    $fA = new query\FieldAlias($fA, 'fieldAAlias');
    $ob = new query\OrderBy;
    $ob->addField($fB);
    $w = new query\Where(new query\LogicOperation($fB, '>', "0"));
    $j = new query\Join(query\Join::INNER, $tC, new query\LogicOperation($fC, '=', $fB));
    // Сбор Select инструкции
    $o = new query\Select;
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
   * @covers query\Select::isReestablish
   */
  public function testIsReestablish(){
    $this->assertTrue(query\Select::isReestablish('SELECT * FROM `tableA`'));
    $this->assertTrue(query\Select::isReestablish('SELECT `fieldA` FROM `tableA`'));
    $this->assertTrue(query\Select::isReestablish('SELECT `fieldA` as fa FROM `tableA`'));
    $this->assertTrue(query\Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB`'));
    $this->assertTrue(query\Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`)'));
    $this->assertTrue(query\Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) LEFT JOIN `tableD` ON (tableD.id = `fieldB`)'));
    $this->assertTrue(query\Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) WHERE ((`fieldB` > "0") OR (tableA.fieldA < "10"))'));
    $this->assertTrue(query\Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) ORDER BY `fieldA`,tableB.fieldB ASC WHERE ((`fieldB` > "0") OR (tableA.fieldA < "10"))'));
    $this->assertTrue(query\Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) ORDER BY `fieldA`,tableB.fieldB DESC LIMIT 10 WHERE ((`fieldB` > "0") OR (tableA.fieldA < "10"))'));
    $this->assertFalse(query\Select::isReestablish('`fieldA` FROM `tableA`'));
    $this->assertFalse(query\Select::isReestablish('SELECT `fieldA` `tableA`'));
    $this->assertFalse(query\Select::isReestablish('SELECT`fieldA` FROM `tableA`'));
    $this->assertFalse(query\Select::isReestablish('SELECT `fieldA` FROM`tableA`'));
    $this->assertFalse(query\Select::isReestablish('SELECT `fieldA` FROM tableA'));
    $this->assertFalse(query\Select::isReestablish('SELECT fieldA FROM `tableA`'));
    $this->assertFalse(query\Select::isReestablish('SELECT `fieldA` tableB.fieldB FROM `tableA`, `tableB`'));
    $this->assertFalse(query\Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA` `tableB`'));
    $this->assertFalse(query\Select::isReestablish('SELECT FROM `tableA`, `tableB`'));
    $this->assertFalse(query\Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM '));
    $this->assertFalse(query\Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB`INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`)'));
    $this->assertFalse(query\Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC`ON(tableC.fieldC = `fieldB`)'));
    $this->assertFalse(query\Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` JOIN `tableC` ON (tableC.fieldC = `fieldB`)'));
    $this->assertFalse(query\Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON ()'));
    $this->assertFalse(query\Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`), LEFT JOIN `tableD` ON(tableD.id = `fieldB`)'));
    $this->assertFalse(query\Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) WHERE((`fieldB` > "0") OR (tableA.fieldA < "10"))'));
    $this->assertFalse(query\Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) ((`fieldB` > "0") OR (tableA.fieldA < "10"))'));
    $this->assertFalse(query\Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) WHERE ()'));
    $this->assertFalse(query\Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) ORDER BY `fieldA`,tableB.fieldB WHERE ((`fieldB` > "0") OR (tableA.fieldA < "10"))'));
    $this->assertFalse(query\Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) ORDER BY `fieldA` tableB.fieldB WHERE ((`fieldB` > "0") OR (tableA.fieldA < "10"))'));
    $this->assertFalse(query\Select::isReestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) ORDER BY `fieldA`,tableB.fieldB LIMIT 0 WHERE ((`fieldB` > "0") OR (tableA.fieldA < "10"))'));
  }

  /**
   * @covers query\Select::reestablish
   */
  public function testReestablish(){
    $o = query\Select::reestablish('SELECT * FROM `tableA`');
    $this->assertEquals([], $o->getFields());
    $o = query\Select::reestablish('SELECT `fieldA` FROM `tableA`');
    $this->assertEquals('fieldA', $o->getFields()[0]->getName());
    $this->assertEquals('tableA', $o->getTables()[0]->getTableName());
    $o = query\Select::reestablish('SELECT `fieldA` as fa FROM `tableA`');
    $this->assertEquals('fieldA', $o->getFields()[0]->getComponent()->getName());
    $this->assertEquals('fa', $o->getFields()[0]->getAlias());
    $o = query\Select::reestablish('SELECT `fieldA`, tableB.fieldB FROM `tableA`, `tableB`');
    $this->assertEquals('tableB', $o->getFields()[1]->getTable()->getTableName());
    $this->assertEquals('tableB', $o->getTables()[1]->getTableName());
    $o = query\Select::reestablish('SELECT `fieldA`, tableB.fieldB FROM `tableA`, `tableB` LIMIT 10');
    $this->assertEquals(10, $o->getLimit()->getCountRow());
    $o = query\Select::reestablish('SELECT `fieldA`, tableB.fieldB FROM `tableA`, `tableB` ORDER BY `fieldA`,tableB.fieldB DESC LIMIT 10');
    $this->assertEquals('DESC', $o->getOrderBy()->getSortedType());
    $this->assertEquals('fieldA', $o->getOrderBy()->getFields()[0]->getName());
    $o = query\Select::reestablish('SELECT `fieldA`, tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) ORDER BY `fieldA`,tableB.fieldB DESC LIMIT 10');
    $this->assertEquals('INNER', $o->getJoins()[0]->getType());
    $o = query\Select::reestablish('SELECT `fieldA`, tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) LEFT JOIN `tableD` ON (tableD.id = `fieldB`) ORDER BY `fieldA`,tableB.fieldB DESC LIMIT 10');
    $this->assertEquals('tableD', $o->getJoins()[1]->getTable()->getTableName());
    $o = query\Select::reestablish('SELECT `fieldA`,tableB.fieldB FROM `tableA`, `tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) LEFT JOIN `tableD` ON (tableD.id = `fieldB`) ORDER BY `fieldA`,tableB.fieldB ASC LIMIT 10 WHERE ((`fieldB` > "0") OR (tableA.fieldA < "10"))');
    $this->assertEquals('0', $o->getWhere()->getCondition()->getLeftOperand()->getValue());
    $this->assertEquals('SELECT `fieldA`,tableB.fieldB FROM `tableA`,`tableB` INNER JOIN `tableC` ON (tableC.fieldC = `fieldB`) LEFT JOIN `tableD` ON (tableD.id = `fieldB`) WHERE ((`fieldB` > "0") OR (tableA.fieldA < "10")) ORDER BY `fieldA`,tableB.fieldB ASC LIMIT 10', $o->interpretation('mysql'));
  }
}
