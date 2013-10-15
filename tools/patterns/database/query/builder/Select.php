<?php
namespace PPHP\tools\patterns\database\query\builder;

use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\classes\standard\storage\database\ORM\Field;
use PPHP\tools\patterns\database\query as query;
use PPHP\tools\patterns\interpreter\Interpreter;
use PPHP\tools\patterns\singleton\Singleton;
use PPHP\tools\patterns\singleton\TSingleton;

/**
 * Класс представляет фабрику объектной SQL инструкции Select.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query\builder
 */
class Select implements Singleton, Interpreter{
  use TSingleton;

  /**
   * @var \PPHP\tools\patterns\database\query\Select Объектная SQL инструкция Select.
   */
  protected $select;

  /**
   * @var \PPHP\tools\patterns\database\query\builder\Where|null Фабрика Where, являющаяся частью запроса, или null - если запрос не имеет условия.
   */
  protected $where;

  /**
   * Метод определяет поля запроса.
   * @param string[]|null $fields [optional] Массив имен полей запроса. Если передан ассоциативный массив, то ключи определяют целевые таблицы полей. Если параметр не передан, выбираются все поля.
   * @return \PPHP\tools\patterns\database\query\builder\Select Вызываемый объект.
   */
  public function fields(array $fields = null){
    if(empty($this->select)){
      $this->select = new query\Select;
    }
    if(is_null($fields)){
      $this->select->addAllField();
    }
    else{
      foreach($fields as $table => $field){
        $field = new query\Field($field);
        if(is_string($table)){
          $field->setTable(new query\Table($table));
        }
        $this->select->addField($field);
      }
    }

    return $this;
  }

  /**
   * Метод определяет целевые таблицы запроса.
   * @param string[] $tables Список имен целевых таблиц запроса.
   * @return \PPHP\tools\patterns\database\query\builder\Select Вызываемый объект.
   */
  public function tables(array $tables){
    if(empty($this->select)){
      $this->select = new query\Select;
    }
    foreach($tables as $table){
      $this->select->addTable(new query\Table($table));
    }

    return $this;
  }

  /**
   * Метод добавляет объединение типа INNER.
   * @param string $table Объединяемая таблица.
   * @param string $leftOperand  Левый операнд.
   * @param string $operator     Оператор. Допустимо одно из следующих значений: >, <, >=, <=, =, !=, in.
   * @param string|array $rightOperand Правый операнд. Может быть массивом, если в качестве оператора передан in. Может быть объектным SQL компонентом Field, если значение обрамлено в косые кавычки (`). Добавляет информацию о целевой таблице поля, если в строке присутствует точка.
   * @return \PPHP\tools\patterns\database\query\builder\Select Вызываемый объект.
   */
  public function innerJoin($table, $leftOperand, $operator, $rightOperand){
    InvalidArgumentException::verifyType($table, 'S');
    $this->select->addJoin(new query\Join(query\Join::INNER, new query\Table($table), Where::createCondition($leftOperand, $operator, $rightOperand)));

    return $this;
  }

  /**
   * Метод добавляет объединение типа CROSS.
   * @param string $table Объединяемая таблица.
   * @param string $leftOperand  Левый операнд.
   * @param string $operator     Оператор. Допустимо одно из следующих значений: >, <, >=, <=, =, !=, in.
   * @param string|array $rightOperand Правый операнд. Может быть массивом, если в качестве оператора передан in. Может быть объектным SQL компонентом Field, если значение обрамлено в косые кавычки (`). Добавляет информацию о целевой таблице поля, если в строке присутствует точка.
   * @return \PPHP\tools\patterns\database\query\builder\Select Вызываемый объект.
   */
  public function crossJoin($table, $leftOperand, $operator, $rightOperand){
    InvalidArgumentException::verifyType($table, 'S');
    $this->select->addJoin(new query\Join(query\Join::CROSS, new query\Table($table), Where::createCondition($leftOperand, $operator, $rightOperand)));

    return $this;
  }

  /**
   * Метод добавляет объединение типа LEFT.
   * @param string $table Объединяемая таблица.
   * @param string $leftOperand  Левый операнд.
   * @param string $operator     Оператор. Допустимо одно из следующих значений: >, <, >=, <=, =, !=, in.
   * @param string|array $rightOperand Правый операнд. Может быть массивом, если в качестве оператора передан in. Может быть объектным SQL компонентом Field, если значение обрамлено в косые кавычки (`). Добавляет информацию о целевой таблице поля, если в строке присутствует точка.
   * @return \PPHP\tools\patterns\database\query\builder\Select Вызываемый объект.
   */
  public function leftJoin($table, $leftOperand, $operator, $rightOperand){
    InvalidArgumentException::verifyType($table, 'S');
    $this->select->addJoin(new query\Join(query\Join::LEFT, new query\Table($table), Where::createCondition($leftOperand, $operator, $rightOperand)));

    return $this;
  }

  /**
   * Метод добавляет объединение типа RIGHT.
   * @param string $table Объединяемая таблица.
   * @param string $leftOperand  Левый операнд.
   * @param string $operator     Оператор. Допустимо одно из следующих значений: >, <, >=, <=, =, !=, in.
   * @param string|array $rightOperand Правый операнд. Может быть массивом, если в качестве оператора передан in. Может быть объектным SQL компонентом Field, если значение обрамлено в косые кавычки (`). Добавляет информацию о целевой таблице поля, если в строке присутствует точка.
   * @return \PPHP\tools\patterns\database\query\builder\Select Вызываемый объект.
   */
  public function rightJoin($table, $leftOperand, $operator, $rightOperand){
    InvalidArgumentException::verifyType($table, 'S');
    $this->select->addJoin(new query\Join(query\Join::RIGHT, new query\Table($table), Where::createCondition($leftOperand, $operator, $rightOperand)));

    return $this;
  }

  /**
   * Метод добавляет объединение типа FULL.
   * @param string $table Объединяемая таблица.
   * @param string $leftOperand  Левый операнд.
   * @param string $operator     Оператор. Допустимо одно из следующих значений: >, <, >=, <=, =, !=, in.
   * @param string|array $rightOperand Правый операнд. Может быть массивом, если в качестве оператора передан in. Может быть объектным SQL компонентом Field, если значение обрамлено в косые кавычки (`). Добавляет информацию о целевой таблице поля, если в строке присутствует точка.
   * @return \PPHP\tools\patterns\database\query\builder\Select Вызываемый объект.
   */
  public function fullJoin($table, $leftOperand, $operator, $rightOperand){
    InvalidArgumentException::verifyType($table, 'S');
    $this->select->addJoin(new query\Join(query\Join::FULL, new query\Table($table), Where::createCondition($leftOperand, $operator, $rightOperand)));

    return $this;
  }

  /**
   * Метод добавляет компонент Limit.
   * @param integer $limit Объем выборки.
   * @throws \Exception|\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return \PPHP\tools\patterns\database\query\builder\Select Вызываемый объект.
   */
  public function limit($limit){
    InvalidArgumentException::verifyType($limit, 'i');
    $this->select->insertLimit(new query\Limit($limit));

    return $this;
  }

  /**
   * Метод добавляет компонент OrderBy.
   * @param string[] $fields Имена полей сортировки.
   * @param string $type [optional] Тип сортировки.
   * @return \PPHP\tools\patterns\database\query\builder\Select Вызываемый объект.
   */
  public function orderBy(array $fields, $type = query\OrderBy::ASC){
    $ob = new query\OrderBy($type);
    foreach($fields as $field){
      $ob->addField(new query\Field($field));
    }
    $this->select->insertOrderBy($ob);

    return $this;
  }

  /**
   * Метод создает объектный SQL компонент Where для данного условия.
   * Метод должен быть вызван только после вызова метода table или fields, формирующего инструкцию.
   * @param string $leftOperand  Левый операнд.
   * @param string $operator     Оператор. Допустимо одно из следующих значений: >, <, >=, <=, =, !=, in.
   * @param string|array $rightOperand Правый операнд. Может быть массивом, если в качестве оператора передан in. Может быть объектным SQL компонентом Field, если значение обрамлено в косые кавычки (`).
   * @throws \Exception|\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается если метод вызывается до вызова метода table или fields.
   * @return \PPHP\tools\patterns\database\query\builder\Where Фабрика объектного SQL компонента Where для данной инструкции.
   */
  public function where($leftOperand, $operator, $rightOperand){
    if(empty($this->select)){
      throw new NotFoundDataException('Невозможно добавить условие отбора без указания целевой таблицы.');
    }
    try{
      /**
       * @var Where $whereBuilder
       */
      $whereBuilder = Where::getInstance();
      $this->where = $whereBuilder->create($leftOperand, $operator, $rightOperand);
    }
    catch(InvalidArgumentException $e){
      throw $e;
    }
    $this->where->select = $this;

    return $this->where;
  }

  /**
   * Метод возвращает полученную объектную SQL инструкцию Select.
   * @return \PPHP\tools\patterns\database\query\Select Результат работы фабрики.
   */
  public function get(){
    $select = $this->select;
    if(isset($this->where)){
      $this->select->insertWhere($this->where->last());
    }
    $this->clear();

    return $select;
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Interpreter
   */
  public function interpretation($driver = null){
    return $this->get()->interpretation($driver);
  }

  /**
   * Метод удаляет текущую конструкцию.
   */
  public function clear(){
    unset($this->select);
    unset($this->where);
  }
}