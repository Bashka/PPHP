<?php
namespace PPHP\tools\patterns\database\query;

/**
 * Логический оператор вхождения значения в указанное множество значений.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class INLogicOperation implements Condition{
  /**
   * Сравниваемое поле.
   * @var Field
   */
  private $field;

  /**
   * Доступные значения.
   * @var string[]
   */
  private $values;

  /**
   * Select запрос, возвращающий искомые данные.
   * @var Select
   */
  private $selectQuery;

  /**
   * @param Field $field Проверяемое поле.
   */
  function __construct(Field $field){
    $this->field = $field;
    $this->values = [];
  }

  /**
   * Метод добавляет значение в список допустимых.
   * @param string $value Добавляемое значение.
   */
  public function addValue($value){
    $this->values[] = $value;
  }

  /**
   * Метод определяет SQL инструкцию, возвращающую список допустимых значений.
   * @param \PPHP\tools\patterns\database\query\Select $selectQuery SQL инструкция, возвращающая список допустимых значений.
   */
  public function setSelectQuery($selectQuery){
    $this->selectQuery = $selectQuery;
  }


  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   * @param string|null $driver Используемая СУБД.
   * @return string Представление элемента в виде части SQL запроса.
   */
  public function interpretation($driver=null){
    return '(' . $this->field->interpretation() . ' IN ("' . ((empty($this->selectQuery))? implode('","', $this->values) : $this->selectQuery->interpretation()) . '"))';
  }
}
