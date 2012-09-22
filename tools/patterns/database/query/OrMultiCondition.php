<?php
namespace PPHP\tools\patterns\database\query;

/**
 * Множественное логическое выражение с OR логическим разделителем.
 */
class OrMultiCondition extends QueryCondition{
  /**
   * Метод должен возвращать объединяющий логический оператор.
   * @return string
   */
  protected function getOperator(){
    return ' OR ';
  }
}
