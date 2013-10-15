<?php
namespace PPHP\tools\patterns\database\query;

/**
 * Множественное логическое выражение с AND логическим разделителем.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class AndMultiCondition extends QueryCondition{
  /**
   * Метод возвращает объединяющий логический оператор И.
   * @static
   * @return string Строка AND.
   */
  protected static function getOperator(){
    return 'AND';
  }
}
