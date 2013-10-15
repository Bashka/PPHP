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
   * @var \PPHP\tools\patterns\database\query\Field Сравниваемое поле.
   */
  private $field;

  /**
   * @var mixed[] Доступные значения (string|integer|float|boolean).
   */
  private $values;

  /**
   * @var \PPHP\tools\patterns\database\query\Select Select запрос, возвращающий искомые данные.
   */
  private $selectQuery;

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getMasks($driver = null){
    return ['\(((?:' . Field::getMasks()[0] . ')|(?:' . Field::getMasks()[1] . ')) IN \((' . LogicOperation::getPatterns()['stringValue'] . '(, ?' . LogicOperation::getPatterns()['stringValue'] . ')*)\)\)'];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Restorable
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
   * @param \PPHP\tools\patterns\database\query\Field $field Проверяемое поле.
   */
  function __construct(Field $field){
    $this->field = $field;
    $this->values = [];
  }

  /**
   * Метод добавляет значение в список допустимых.
   * @param string|integer|float|boolean $value Добавляемое значение.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return \PPHP\tools\patterns\database\query\INLogicOperation Метод возвращает вызываемый объект.
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
   * @param \PPHP\tools\patterns\database\query\Select $selectQuery SQL инструкция, возвращающая список допустимых значений.
   * @return \PPHP\tools\patterns\database\query\INLogicOperation Метод возвращает вызываемый объект.
   */
  public function setSelectQuery(Select $selectQuery){
    $this->selectQuery = $selectQuery;

    return $this;
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Interpreter
   */
  public function interpretation($driver = null){
    exceptions\InvalidArgumentException::verifyType($driver, 'Sn');
    if(empty($this->selectQuery) && count($this->values) == 0){
      throw new exceptions\NotFoundDataException('Для интерпретации объекта необходимо определить хотя бы одно значение.');
    }
    try{
      if(empty($this->selectQuery)){
        $values = '"' . implode('","', $this->values) . '"';
      }
      else{
        $values = $this->selectQuery->interpretation($driver);
      }

      return '(' . $this->field->interpretation($driver) . ' IN (' . $values . '))';
    }
    catch(exceptions\NotFoundDataException $exc){
      throw $exc;
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * @return \PPHP\tools\patterns\database\query\Field
   */
  public function getField(){
    return $this->field;
  }

  /**
   * @return \PPHP\tools\patterns\database\query\Select
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
