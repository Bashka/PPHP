<?php
namespace PPHP\tools\patterns\database\query;

/**
 * Множественное логическое выражение с OR логическим разделителем.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class OrMultiCondition extends QueryCondition{
  /**
   * Метод возвращает объединяющий логический оператор ИЛИ.
   * @static
   * @return string Строка OR.
   */
  protected static function getOperator(){
    return 'OR';
  }
}
