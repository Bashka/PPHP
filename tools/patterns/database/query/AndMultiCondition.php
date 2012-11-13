<?php
namespace PPHP\tools\patterns\database\query;

/**
 * Множественное логическое выражение с AND логическим разделителем.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class AndMultiCondition extends QueryCondition{
  /**
   * Метод должен возвращать объединяющий логический оператор И.
   * @return string Представление элемента в виде части SQL запроса.
   */
  protected function getOperator(){
    return ' AND ';
  }
}
