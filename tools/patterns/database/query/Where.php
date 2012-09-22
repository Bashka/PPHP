<?php
namespace PPHP\tools\patterns\database\query;

/**
 * Класс представляет условие в SQL запросе.
 */
class Where implements ComponentQuery{
  /**
   * Логическая операция.
   * @var Condition
   */
  private $condition;

  /**
   * @param Condition $condition
   */
  function __construct(Condition $condition){
    $this->condition = $condition;
  }

  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   * @param string|null $driver Используемая СУБД.
   * @return string Представление элемента в виде части SQL запроса.
   */
  public function interpretation($driver=null){
    return 'WHERE ' . $this->condition->interpretation();
  }

}
