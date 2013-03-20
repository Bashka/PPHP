<?php
namespace PPHP\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

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
   *
   * @param string|integer|float|boolean $value Добавляемое значение.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  public function addValue($value){
    if(!is_string($value) && !is_integer($value) && !is_float($value) && !is_bool($value)){
      throw new exceptions\InvalidArgumentException('Неверный тип аргумента, ожидается string, integer, float, boolean.');
    }
    $this->values[] = $value;
  }

  /**
   * Метод определяет SQL инструкцию, возвращающую список допустимых значений.
   * @param Select $selectQuery SQL инструкция, возвращающая список допустимых значений.
   */
  public function setSelectQuery(Select $selectQuery){
    $this->selectQuery = $selectQuery;
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
      return '(' . $this->field->interpretation($driver) . ' IN ("' . ((empty($this->selectQuery))? implode('","', $this->values) : $this->selectQuery->interpretation($driver)) . '"))';
    }
    catch(exceptions\NotFoundDataException $exc){
      throw $exc;
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }
}
