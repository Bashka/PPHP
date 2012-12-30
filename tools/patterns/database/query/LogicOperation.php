<?php
namespace PPHP\tools\patterns\database\query;

/**
 * Логический оператор сравнения.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class LogicOperation implements Condition{
  /**
   * Сравниваемое поле.
   * @var Field
   */
  private $field;
  /**
   * Оператор сравнения. Одно из следующих значений: =, !=, >=, <=, >, <.
   * @var string
   */
  private $operator;
  /**
   * Правый операнд.
   * @var string|number|boolean|Field
   */
  private $value;

  /**
   * @param Field $field Сравниваемое поле.
   * @param string $operator Оператор сравнения. Одно из следующих значений: =, !=, >=, <=, >, <.
   * @param string|number|boolean|Field $value Правый операнд.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct(Field $field, $operator, $value){
    if(array_search($operator, ['=', '>=', '<=', '!=', '>', '<']) == -1){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException();
    }
    if(is_array($value) || (is_object($value) && !($value instanceof Field))){

      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException();
    }

    $this->field = $field;
    $this->operator = $operator;
    $this->value = $value;
  }


  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   * @param string|null $driver Используемая СУБД.
   * @return string Представление элемента в виде части SQL запроса.
   */
  public function interpretation($driver=null){
    return '(' . $this->field->interpretation() . ' ' . $this->operator . ' ' . (($this->value instanceof Field)? $this->value->interpretation() : '"' . $this->value . '"') . ')';
  }

}
