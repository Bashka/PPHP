<?php
namespace PPHP\tools\patterns\database\query;

/**
 * Множественное логическое выражение с AND логическим разделителем.
 */
class AndMultiCondition extends QueryCondition{
  /**
   * Метод должен возвращать объединяющий логический оператор.
   * @return string
   */
  protected function getOperator(){
    return ' AND ';
  }
}
