<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет логическое выражение.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
abstract class QueryCondition extends Condition{
  /**
   * @var \PPHP\tools\patterns\database\query\Condition[] Множество условий, входящих в логическое выражение.
   */
  protected $conditions;

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getMasks($driver = null){
    return ['\(' . Condition::getPatterns()['condition'] . ' ' . static::getPatterns()['moreCondition'] . '(?: ' . static::getPatterns()['moreCondition'] . ')*\)'];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getPatterns($driver = null){
    return ['moreCondition' => static::getOperator() . ' ' . Condition::getPatterns()['condition']];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Restorable
   */
  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    parent::reestablish($string);
    /**
     * @var QueryCondition $o
     */
    $o = new static();
    $conditions = explode(static::getOperator(), substr($string, 1, -1));
    foreach($conditions as $condition){
      $o->addCondition(Condition::reestablishCondition(trim($condition)));
    }

    return $o;
  }

  /**
   * Метод должен возвращать объединяющий логический оператор.
   * @static
   * @return string
   */
  protected static function getOperator(){
    return '';
  }

  function __construct(){
    $this->conditions = [];
  }

  /**
   * Метод добавляет логическую операцию в выражение.
   * @param \PPHP\tools\patterns\database\query\Condition $condition Логический оператор.
   * @return $this Метод возвращает вызываемый объект.
   */
  public function addCondition(Condition $condition){
    $this->conditions[] = $condition;

    return $this;
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Interpreter
   */
  public function interpretation($driver = null){
    exceptions\InvalidArgumentException::verifyType($driver, 'Sn');
    if(count($this->conditions) < 2){
      throw new exceptions\NotFoundDataException('Недостаточное число условий в выражении.');
    }
    $operator = static::getOperator();
    $conditions = [];
    foreach($this->conditions as $condition){
      try{
        $conditions[] = $condition->interpretation($driver);
      }
      catch(exceptions\NotFoundDataException $e){
        throw $e;
      }
      catch(exceptions\InvalidArgumentException $e){
        throw $e;
      }
    }

    return '(' . implode(' ' . $operator . ' ', $conditions) . ')';
  }

  /**
   * @return Condition[]
   */
  public function getConditions(){
    return $this->conditions;
  }
}
