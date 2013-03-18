<?php
namespace PPHP\tools\patterns\database\query;

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
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct(Condition $leftOperand, $logicOperator, Condition $rightOperand){
    if(array_search($logicOperator, ['AND', 'OR']) == -1){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException();
    }
    $this->leftOperand = $leftOperand;
    $this->logicOperator = $logicOperator;
    $this->rightOperand = $rightOperand;
  }

  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   * @param string|null $driver Используемая СУБД.
   * @return string Представление элемента в виде части SQL запроса.
   */
  public function interpretation($driver=null){
    return '(' . $this->leftOperand->interpretation() . ' ' . $this->logicOperator . ' ' . $this->rightOperand->interpretation() . ')';
  }
}