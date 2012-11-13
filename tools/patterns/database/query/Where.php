<?php
namespace PPHP\tools\patterns\database\query;

/**
 * Класс представляет условие в SQL запросе.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Where implements ComponentQuery{
  /**
   * Логическая операция.
   * @var Condition
   */
  private $condition;

  /**
   * @param Condition $condition Логическая операция.
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
