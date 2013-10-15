<?php
namespace PPHP\tools\patterns\database\query\builder;

use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\patterns\database\query as query;
use PPHP\tools\patterns\singleton\Singleton;
use PPHP\tools\patterns\singleton\TSingleton;

/**
 * Класс представляет фабрику объектного SQL компонента Where.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query\builder
 */
class Where implements Singleton{
  use TSingleton;

  /**
   * @var \SplStack Стек логических выражений.
   */
  protected $conditions;

  /**
   *
   */
  private function __construct(){
    $this->conditions = new \SplStack;
  }

  /**
   * Метод формирует объектный SQL компонент условия.
   * @param string $leftOperand  Левый операнд.
   * @param string $operator     Оператор. Допустимо одно из следующих значений: >, <, >=, <=, =, !=, in.
   * @param string|array $rightOperand Правый операнд. Может быть массивом, если в качестве оператора передан in. Может быть объектным SQL компонентом Field, если значение обрамлено в косые кавычки (`). Добавляет информацию о целевой таблице поля, если в строке присутствует точка.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return query\INLogicOperation|query\LogicOperation Объектный SQL компонент условия
   */
  public static function createCondition($leftOperand, $operator, $rightOperand){
    InvalidArgumentException::verifyType($leftOperand, 'S');
    InvalidArgumentException::verifyType($operator, 'S');
    InvalidArgumentException::verifyVal($operator, 's # (>)|(<)|(>=)|(<=)|(=)|(!=)|(in)');
    InvalidArgumentException::verifyType($rightOperand, 'Sa');
    // Формирование левого операнда.
    if(strpos($leftOperand, '.') !== false){
      $leftOperand = explode('.', $leftOperand);
      $table = new query\Table($leftOperand[0]);
      $leftOperand = new query\Field($leftOperand[1]);
      $leftOperand->setTable($table);
    }
    else{
      $leftOperand = new query\Field($leftOperand);
    }
    // Формирование правого операнда.
    if($rightOperand[0] == '`' && $rightOperand[strlen($rightOperand) - 1] == '`'){
      $rightOperand = substr($rightOperand, 1, -1);
      if(strpos($rightOperand, '.') !== false){
        $rightOperand = explode('.', $rightOperand);
        $table = new query\Table($rightOperand[0]);
        $rightOperand = new query\Field($rightOperand[1]);
        $rightOperand->setTable($table);
      }
      else{
        $rightOperand = new query\Field($rightOperand);
      }
    }
    if($operator == 'in'){
      InvalidArgumentException::verifyType($rightOperand, 'a');
      $inLO = new query\INLogicOperation($leftOperand);
      foreach($rightOperand as $value){
        $inLO->addValue($value);
      }

      return $inLO;
    }
    else{
      return new query\LogicOperation($leftOperand, $operator, $rightOperand);
    }
  }

  /**
   * Метод создает новое условное выражение помещая его в стек.
   * @param string $leftOperand  Левый операнд.
   * @param string $operator     Оператор. Допустимо одно из следующих значений: >, <, >=, <=, =, !=, in.
   * @param string|array $rightOperand Правый операнд. Может быть массивом, если в качестве оператора передан in. Может быть объектным SQL компонентом Field, если значение обрамлено в косые кавычки (`).
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return Where Выбываемый объект.
   */
  public function create($leftOperand, $operator, $rightOperand){
    try{
      $this->conditions->push(self::createCondition($leftOperand, $operator, $rightOperand));
    }
    catch(InvalidArgumentException $e){
      throw $e;
    }

    return $this;
  }

  /**
   * Метод создает логическое выражение из текущего логического выражения, переданного условия и оператора И.
   * @param string $leftOperand  Левый операнд.
   * @param string $operator     Оператор. Допустимо одно из следующих значений: >, <, >=, <=, =, !=, in.
   * @param string|array $rightOperand Правый операнд. Может быть массивом, если в качестве оператора передан in. Может быть объектным SQL компонентом Field, если значение обрамлено в косые кавычки (`).
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return Where Выбываемый объект.
   */
  public function andC($leftOperand, $operator, $rightOperand){
    try{
      $this->conditions->push(new query\MultiCondition($this->conditions->pop(), 'AND', self::createCondition($leftOperand, $operator, $rightOperand)));
    }
    catch(InvalidArgumentException $e){
      throw $e;
    }

    return $this;
  }

  /**
   * Метод создает логическое выражение из текущего логического выражения, переданного условия и оператора ИЛИ.
   * @param string $leftOperand  Левый операнд.
   * @param string $operator     Оператор. Допустимо одно из следующих значений: >, <, >=, <=, =, !=, in.
   * @param string|array $rightOperand Правый операнд. Может быть массивом, если в качестве оператора передан in. Может быть объектным SQL компонентом Field, если значение обрамлено в косые кавычки (`).
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return Where Выбываемый объект.
   */
  public function orC($leftOperand, $operator, $rightOperand){
    try{
      $this->conditions->push(new query\MultiCondition($this->conditions->pop(), 'OR', self::createCondition($leftOperand, $operator, $rightOperand)));
    }
    catch(InvalidArgumentException $e){
      throw $e;
    }

    return $this;
  }

  /**
   * Метод объединяет текущее логическое выражение с предыдущем с помощью указанного оператора или возвращает объектный SQL компонент Where с текущим логическим выражением если парамент не передан.
   * @param string|null $operator Оператор объединения. Допустимые значения: AND, OR.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return Where|query\Where Выбываемый объект или созданный объектный SQL компонент Where.
   */
  public function last($operator = null){
    InvalidArgumentException::verifyType($operator, 'Sn');
    if(!is_null($operator)){
      InvalidArgumentException::verifyVal($operator, 's # (AND)|(OR)');
      $condition = $this->conditions->pop();
      $this->conditions->push(new query\MultiCondition($this->conditions->pop(), $operator, $condition));

      return $this;
    }
    else{
      $where = new query\Where($this->conditions->pop());
      $this->conditions = new \SplStack;

      return $where;
    }
  }

  /**
   * @return \SplStack
   */
  public function getConditions(){
    return $this->conditions;
  }

  /**
   * Метод удаляет все содержимое стека логических выражений.
   */
  public function clear(){
    $this->conditions = new \SplStack;
  }
}