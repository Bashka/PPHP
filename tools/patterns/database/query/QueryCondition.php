<?php
namespace PPHP\tools\patterns\database\query;

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
   * @param string|null $driver Используемая СУБД.
   * @throws StandardException Выбрасывается в случае, если произведена попытка интерпретации пустого выражения.
   * @return string Представление элемента в виде части SQL запроса.
   */
  public function interpretation($driver=null){
    if($this->conditions->count() == 0){
      throw new StandardException('Недостаточное число условий в выражении.');
    }
    $resultMulticondition = '(';
    foreach($this->conditions as $condition){
      $resultMulticondition .= $condition->interpretation() . $this->getOperator();
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
