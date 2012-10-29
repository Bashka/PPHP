<?php
namespace PPHP\tools\patterns\database\query;

/**
 * Классы, реализующие данный интерфейс должны иметь механизм интерпретации себя в строку SQL запроса.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
interface ComponentQuery{
  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   * @abstract
   * @param string|null $driver Используемая СУБД.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws StandardException Выбрасывается в случае, если отсутствуют обязательные компоненты запроса.
   * @return string Представление элемента в виде части SQL запроса.
   */
  public function interpretation($driver=null);
}
