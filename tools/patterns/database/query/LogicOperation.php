<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\classes\standard\baseType\String;

/**
 * Логический оператор сравнения.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class LogicOperation extends Condition{
  /**
   * @var \PPHP\tools\patterns\database\query\Field Сравниваемое поле.
   */
  private $field;

  /**
   * Оператор сравнения. Одно из следующих значений: =, !=, >=, <=, >, <.
   * @var string
   */
  private $operator;

  /**
   * @var string|number|boolean|\PPHP\tools\patterns\database\query\Field Правый операнд.
   */
  private $value;

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getMasks($driver = null){
    return ['\((?:(?:' . Field::getMasks()[0] . ')|(?:' . Field::getMasks()[1] . ')) ' . self::getPatterns()['operator'] . ' ' . self::getPatterns()['stringValue'] . '\)', '\((?:(?:' . Field::getMasks()[0] . ')|(?:' . Field::getMasks()[1] . ')) ' . self::getPatterns()['operator'] . ' (?:(?:' . Field::getMasks()[0] . ')|(?:' . Field::getMasks()[1] . '))\)'];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getPatterns($driver = null){
    return ['operator' => '(?:=|!=|>=|<=|>|<)', 'stringValue' => '"[^"]*"'];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Restorable
   */
  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    $mask = parent::reestablish($string);
    $string = trim(substr($string, 1, -1)); // Исключение обрамляющих круглых скобок
    $string = new String($string);
    $lField = $string->nextComponent(' ')->getVal();
    $operator = trim($string->nextComponent(' ')->getVal());
    $value = trim($string->sub()->getVal());
    // Выброс исключений невозможен
    $lField = Field::reestablish($lField);
    if($mask['key'] == 1){
      // Выброс исключений невозможен
      $value = Field::reestablish($value);
    }
    elseif($mask['key'] == 0){
      $value = substr(substr($value, 1), 0, -1);
    }

    return new static($lField, $operator, $value);
  }

  /**
   * @param \PPHP\tools\patterns\database\query\Field $field Сравниваемое поле.
   * @param string $operator Оператор сравнения. Одно из следующих значений: =, !=, >=, <=, >, <.
   * @param string|number|boolean|float|\PPHP\tools\patterns\database\query\Field $value Правый операнд.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct(Field $field, $operator, $value){
    try{
      exceptions\InvalidArgumentException::verifyType($value, 'sifb');
    }
    catch(exceptions\InvalidArgumentException $e){
      if(!($value instanceof Field)){
        throw $e;
      }
    }
    exceptions\InvalidArgumentException::verifyVal($operator, 's # =|>=|<=|!=|>|<');
    $this->field = $field;
    $this->operator = $operator;
    $this->value = $value;
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Interpreter
   */
  public function interpretation($driver = null){
    if($this->value instanceof Field){
      $value = $this->value->interpretation($driver);
    }
    else{
      $value = '"' . (string) $this->value . '"';
    }

    return '(' . $this->field->interpretation($driver) . ' ' . $this->operator . ' ' . $value . ')';
  }

  /**
   * @return \PPHP\tools\patterns\database\query\Field
   */
  public function getField(){
    return $this->field;
  }

  /**
   * @return string
   */
  public function getOperator(){
    return $this->operator;
  }

  /**
   * @return string|number|boolean|\PPHP\tools\patterns\database\query\Field
   */
  public function getValue(){
    return $this->value;
  }
}
