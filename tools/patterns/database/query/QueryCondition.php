<?php
namespace PPHP\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет логическое выражение.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
abstract class QueryCondition extends Condition{
  /**
   * Множество условий, входящих в логическое выражение.
   * @var Condition[]
   */
  protected $conditions;

  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return ['\('.Condition::getPatterns()['condition'].' '.static::getPatterns()['moreCondition'].'(?: '.static::getPatterns()['moreCondition'].')*\)'];
  }

  /**
   * Метод возвращает массив шаблонов, описывающих различные компоненты шаблонов верификации.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getPatterns($driver = null){
    return ['moreCondition' => static::getOperator().' '.Condition::getPatterns()['condition']];
  }

  /**
   * Метод восстанавливает объект из строки.
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws exceptions\StructureException Выбрасывается в случае, если исходная строка не отвечает требования структуры.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return static Результирующий объект.
   */
  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    parent::reestablish($string);

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
   * @param Condition $condition Логический оператор.
   * @return $this Метод возвращает вызываемый объект.
   */
  public function addCondition(Condition $condition){
    $this->conditions[] = $condition;
    return $this;
  }

  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   *
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   *
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты объекта.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver=null){
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
    return '('.implode(' '.$operator.' ', $conditions).')';
  }

  /**
   * @return Condition[]
   */
  public function getConditions(){
    return $this->conditions;
  }
}
