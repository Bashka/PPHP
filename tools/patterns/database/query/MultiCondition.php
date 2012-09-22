<?php
namespace PPHP\tools\patterns\database\query;

/**
 * Множественное логическое выражение.
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
   * Логический оператор.
   * @var string
   */
  private $logicOperator;

  /**
   * @param Condition $leftOperand
   * @param string $logicOperator AND, OR
   * @param Condition $rightOperand
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если один из аргументов имеет недопустимое значение.
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