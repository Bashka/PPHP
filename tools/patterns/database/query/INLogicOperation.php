<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Логический оператор вхождения значения в указанное множество значений.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class INLogicOperation extends Condition{
  /**
   * Сравниваемое поле.
   * @var Field
   */
  private $field;

  /**
   * Доступные значения string|integer|float|boolean.
   * @var mixed[]
   */
  private $values;

  /**
   * Select запрос, возвращающий искомые данные.
   * @var Select
   */
  private $selectQuery;

  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return ['\(((?:' . Field::getMasks()[0] . ')|(?:' . Field::getMasks()[1] . ')) IN \((' . LogicOperation::getPatterns()['stringValue'] . '(, ?' . LogicOperation::getPatterns()['stringValue'] . ')*)\)\)'];
  }

  /**
   * Метод восстанавливает объект из строки.
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws exceptions\StructureException Выбрасывается в случае, если исходная строка не отвечает требования структуры.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return static Результирующий объект.
   */
  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    $mask = parent::reestablish($string);
    $o = new self(Field::reestablish($mask[1]));
    $values = explode(',', $mask[2]);
    foreach($values as $value){
      $o->addValue(substr(trim($value), 1, -1));
    }

    return $o;
  }

  /**
   * @param Field $field Проверяемое поле.
   */
  function __construct(Field $field){
    $this->field = $field;
    $this->values = [];
  }

  /**
   * Метод добавляет значение в список допустимых.
   * @param string|integer|float|boolean $value Добавляемое значение.
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return $this Метод возвращает вызываемый объект.
   */
  public function addValue($value){
    exceptions\InvalidArgumentException::verifyType($value, 'sifb');
    if(is_bool($value)){
      $value = 'true';
    }
    $this->values[] = $value;

    return $this;
  }

  /**
   * Метод определяет SQL инструкцию, возвращающую список допустимых значений.
   * @param Select $selectQuery SQL инструкция, возвращающая список допустимых значений.
   * @return $this Метод возвращает вызываемый объект.
   */
  public function setSelectQuery(Select $selectQuery){
    $this->selectQuery = $selectQuery;

    return $this;
  }

  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты объекта.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver = null){
    exceptions\InvalidArgumentException::verifyType($driver, 'Sn');
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

  /**
   * @return Field
   */
  public function getField(){
    return $this->field;
  }

  /**
   * @return Select
   */
  public function getSelectQuery(){
    return $this->selectQuery;
  }

  /**
   * @return mixed[]
   */
  public function getValues(){
    return $this->values;
  }
}
