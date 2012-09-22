<?php
namespace PPHP\tools\patterns\database\query;

/**
 * Классы, реализующие данный интерфейс должны иметь механизм интерпретации себя в строку SQL запроса.
 */
interface ComponentQuery{
  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   * @abstract
   * @param string|null $driver Используемая СУБД.
   * @return string Представление элемента в виде части SQL запроса.
   */
  public function interpretation($driver=null);
}
