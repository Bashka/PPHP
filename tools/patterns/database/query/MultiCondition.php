<?php
namespace PPHP\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Множественное логическое выражение.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class MultiCondition implements Condition{
  /**
   * Левый логический операнд.
   * @var Condition
   */
  private $leftOperand;
  /**
   * Правый логический операнд.
   * @var Condition
   */
  private $rightOperand;
  /**
   * Логический оператор. Одно из следующих значений: AND, OR.
   * @var string
   */
  private $logicOperator;

  /**
   * @param Condition $leftOperand Левый логический операнд.
   * @param string $logicOperator Логический оператор. Одно из следующих значений: AND, OR.
   * @param Condition $rightOperand Правый логический операнд.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct(Condition $leftOperand, $logicOperator, Condition $rightOperand){
    if(array_search($logicOperator, ['AND', 'OR']) == -1){
      throw new exceptions\InvalidArgumentException('Недопустимое значение аргумента. Ожидается AND или OR.');
    }
    $this->leftOperand = $leftOperand;
    $this->logicOperator = $logicOperator;
    $this->rightOperand = $rightOperand;
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
    try{
      return '(' . $this->leftOperand->interpretation($driver) . ' ' . $this->logicOperator . ' ' . $this->rightOperand->interpretation($driver) . ')';
    }
    catch(exceptions\NotFoundDataException $exc){
      throw $exc;
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }
}