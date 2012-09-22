<?php
namespace PPHP\tools\patterns\database\query;

/**
 * Логический оператор сравнения.
 */
class LogicOperation implements Condition{
  /**
   * Сравниваемое поле.
   * @var Field
   */
  private $field;
  /**
   * Оператор сравнения
   * @var string =, !=, >=, <=, >, <
   */
  private $operator;
  /**
   * Левый операнд.
   * @var string|number|boolean|Field
   */
  private $value;

  /**
   * @param Field $field
   * @param string $operator =, !=, >=, <=, >, <
   * @param string|number|boolean|Field $value
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если один из аргументов имеет недопустимое значение.
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
