<?php
namespace PPHP\tools\patterns\database\query;

/**
 * Логический оператор вхождения значения в указанное множество значений.
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
   * @param Field $field
   */
  function __construct(Field $field){
    $this->field = $field;
    $this->values = [];
  }

  public function addValue($value){
    $this->values[] = $value;
  }

  /**
   * @param \PPHP\tools\patterns\database\query\Select $selectQuery
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
