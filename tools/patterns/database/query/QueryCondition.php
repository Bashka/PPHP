<?php
namespace PPHP\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет логическое выражение.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
abstract class QueryCondition implements Condition{
  /**
   * Множество условий, входящих в логическое выражение.
   * @var \SplObjectStorage
   */
  protected $conditions;

  function __construct(){
    $this->conditions = new \SplObjectStorage();
  }

  /**
   * Метод добавляет логическую операцию в выражение.
   * @param Condition $condition Логический оператор.
   */
  public function addCondition(Condition $condition){
    $this->conditions->attach($condition);
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
    if($this->conditions->count() == 0){
      throw new exceptions\NotFoundDataException('Недостаточное число условий в выражении.');
    }
    $resultMulticondition = '(';
    foreach($this->conditions as $condition){
      try{
        $resultMulticondition .= $condition->interpretation($driver) . $this->getOperator();
      }
      catch(exceptions\NotFoundDataException $exc){
        throw $exc;
      }
      catch(exceptions\InvalidArgumentException $exc){
        throw $exc;
      }
    }
    $resultMulticondition = substr($resultMulticondition, 0, strlen($resultMulticondition) - strlen($this->getOperator()));
    $resultMulticondition .= ')';
    return $resultMulticondition;
  }

  /**
   * Метод должен возвращать объединяющий логический оператор.
   * @abstract
   * @return string
   */
  abstract protected function getOperator();
}
