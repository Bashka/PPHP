<?php
namespace PPHP\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

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
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct(Field $field, $operator, $value){
    if(array_search($operator, ['=', '>=', '<=', '!=', '>', '<']) == -1){
      throw new exceptions\InvalidArgumentException('Недопустимое значение параметра. Ожидается =, >=, <=, !=, >, <');
    }
    if(is_array($value) || (is_object($value) && !($value instanceof Field))){
      throw new exceptions\InvalidArgumentException('Неверный тип аргумента, ожидается string, number, Field или boolean.');
    }

    $this->field = $field;
    $this->operator = $operator;
    $this->value = $value;
  }


  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   *
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   *
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты объекта.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver=null){
    try{
      return '(' . $this->field->interpretation($driver) . ' ' . $this->operator . ' ' . (($this->value instanceof Field)? $this->value->interpretation($driver) : '"' . $this->value . '"') . ')';
    }
    catch(exceptions\NotFoundDataException $exc){
      throw $exc;
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

}
