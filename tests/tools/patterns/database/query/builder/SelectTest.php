<?php
namespace PPHP\tests\tools\patterns\database\query\builder;

use PPHP\tools\patterns\database\query\builder\Select;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class SelectTest extends \PHPUnit_Framework_TestCase{
  protected function setUp(){
    Select::getInstance()->clear();
  }

  /**
   * Должен формировать объектную SQL инструкцию PPHP\tools\patterns\database\query\Select.
   * @covers \PPHP\tools\patterns\database\query\builder\Select::fields
   */
  public function testShouldCreateObject(){
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\Select', Select::getInstance()->fields()->get());
  }

  /**
   * Если параметр не передан, должен устанавливать все поля.
   * @covers \PPHP\tools\patterns\database\query\builder\Select::fields
   */
  public function testShouldAddAllFieldsIfNoArgs(){
    $this->assertTrue(Select::getInstance()->fields()->get()->isAllFields());
  }

  /**
   * Если параметр передан, должен устанавливать перечисленные в нем поля.
   * @covers \PPHP\tools\patterns\database\query\builder\Select::fields
   */
  public function testShouldAddFieldsIfArgs(){
    $this->assertEquals('id', Select::getInstance()->fields(['id'])->get()->getFields()[0]->getName());
  }

  /**
   * Если в качестве параметра передан ассоциативный массив, должен устанавливать целевые таблицы.
   * @covers \PPHP\tools\patterns\database\query\builder\Select::fields
   */
  public function testShouldAddFieldTables(){
    $this->assertEquals('people', Select::getInstance()->fields(['people' => 'id'])->get()->getFields()[0]->getTable()->getTableName());
  }

  /**
   * Должен формировать объектную SQL инструкцию PPHP\tools\patterns\database\query\Select.
   * @covers \PPHP\tools\patterns\database\query\builder\Select::table
   */
  public function testShouldCreateObject2(){
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\Select', Select::getInstance()->tables(['people'])->get());
  }

  /**
   * Должен устанавливать перечисленные в нем таблицы.
   * @covers \PPHP\tools\patterns\database\query\builder\Select::table
   */
  public function testShouldAddTables(){
    $this->assertEquals('people', Select::getInstance()->tables(['people'])->get()->getTables()[0]->getTableName());
  }

  /**
   * Должен устанавливать компонент Limit.
   * @covers \PPHP\tools\patterns\database\query\builder\Select::limit
   */
  public function testShouldAddLimit(){
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\Limit', Select::getInstance()->tables(['people'])->limit(5)->get()->getLimit());
  }

  /**
   * Должен устанавливать компонент OrderBy.
   * @covers \PPHP\tools\patterns\database\query\builder\Select::orderBy
   */
  public function testShouldAddOrderBy(){
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\OrderBy', Select::getInstance()->tables(['people'])->orderBy(['name', 'phone'])->get()->getOrderBy());
  }

  /**
   * Должен добавлять компонент Join типа Inner.
   * @covers \PPHP\tools\patterns\database\query\builder\Select::innerJoin
   */
  public function testShouldAddInnerJoin(){
    $join = Select::getInstance()->tables(['people'])->innerJoin('student', 'people.id', '=', '`student.id`')->get()->getJoins()[0];
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\Join', $join);
    $this->assertEquals('INNER', $join->getType());
  }

  /**
   * Должен добавлять компонент Join типа Cross.
   * @covers \PPHP\tools\patterns\database\query\builder\Select::crossJoin
   */
  public function testShouldAddCrossJoin(){
    $join = Select::getInstance()->tables(['people'])->crossJoin('student', 'people.id', '=', '`student.id`')->get()->getJoins()[0];
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\Join', $join);
    $this->assertEquals('CROSS', $join->getType());
  }

  /**
   * Должен добавлять компонент Join типа Left.
   * @covers \PPHP\tools\patterns\database\query\builder\Select::leftJoin
   */
  public function testShouldAddLeftJoin(){
    $join = Select::getInstance()->tables(['people'])->leftJoin('student', 'people.id', '=', '`student.id`')->get()->getJoins()[0];
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\Join', $join);
    $this->assertEquals('LEFT', $join->getType());
  }

  /**
   * Должен добавлять компонент Join типа Right.
   * @covers \PPHP\tools\patterns\database\query\builder\Select::rightJoin
   */
  public function testShouldAddRightJoUpdatein(){
    $join = Select::getInstance()->tables(['people'])->rightJoin('student', 'people.id', '=', '`student.id`')->get()->getJoins()[0];
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\Join', $join);
    $this->assertEquals('RIGHT', $join->getType());
  }

  /**
   * Должен добавлять компонент Join типа Full.
   * @covers \PPHP\tools\patterns\database\query\builder\Select::fullJoin
   */
  public function testShouldAddFullJoin(){
    $join = Select::getInstance()->tables(['people'])->fullJoin('student', 'people.id', '=', '`student.id`')->get()->getJoins()[0];
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\Join', $join);
    $this->assertEquals('FULL', $join->getType());
  }

  /**
   * Должен возвращать сформированную объектную SQL инструкцию PPHP\tools\patterns\database\query\Select.
   * @covers \PPHP\tools\patterns\database\query\builder\Select::get
   */
  public function testShouldReturnCreateObject(){
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\Select', Select::getInstance()->fields()->tables(['people'])->get());
  }

  /**
   * Должен возвращать SQL инструкцию Select в виде строки.
   * @covers \PPHP\tools\patterns\database\query\builder\Select::interpretation
   */
  public function testShouldReturnSQLString(){
    $this->assertEquals('SELECT * FROM `people` INNER JOIN `student` ON (people.id = student.id)  ORDER BY `name`,`phone` ASC LIMIT 10', Select::getInstance()->fields()->tables(['people'])->limit(10)->orderBy(['name', 'phone'])->innerJoin('student', 'people.id', '=', '`student.id`')->interpretation('mysql'));
    $this->assertEquals('SELECT `name`,`phone` FROM `people`  WHERE (`id` < "10")', Select::getInstance()->fields(['name', 'phone'])->tables(['people'])->where('id', '<', '10')->select->interpretation('mysql'));
  }

  /**
   * Должен возвращать объект класса \PPHP\tools\patterns\database\query\builder\Where с указанным условием.
   * @covers \PPHP\tools\patterns\database\query\builder\Select::where
   */
  public function testShouldReturnObjectWhere(){
    $o = Select::getInstance()->fields()->tables(['people'])->where('name', '=', 'ivan');
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\builder\Where', $o);
    $this->assertEquals('name', $o->last()->getCondition()->getField()->getName());
  }

  /**
   * Должен добавлять свойство select, ссылающееся на фабрику, объекту класса \PPHP\tools\patterns\database\query\builder\Where.
   * @covers \PPHP\tools\patterns\database\query\builder\Select::where
   */
  public function testShouldAddProperty(){
    $o = Select::getInstance()->fields()->tables(['people'])->where('name', '=', 'ivan');
    $this->assertTrue(isset($o->select));
    $this->assertInstanceOf('PPHP\tools\patterns\database\query\builder\Select', $o->select);
  }

  /**
   * Должен выбрасывать исключение при вызове до метода table или fields.
   * @covers \PPHP\tools\patterns\database\query\builder\Select::where
   */
  public function testShouldThrowExceptionIfCallBeforeTableMethod(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException');
    $o = Select::getInstance()->where('id', '>', '5');
  }
}
