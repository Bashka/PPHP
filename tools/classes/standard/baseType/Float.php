<?php
namespace PPHP\tools\classes\standard\baseType;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс-обертка служит для предоставления дополнительной логики дробным числам.
 * Допустимый тип: тип integer; тип float; тип string, если строка содержит только цифры, дробную точку с цифрами на месте дробной части или/и ведущий символ минуса.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType
 */
class Float extends Wrapper{
  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return ['-?(?:(?:0)|(?:[1-9]))([0-9]*)(.[0-9]+)'];
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
    parent::reestablish($string);

    return new self((float) $string);
  }

  function __construct($val){
    if(!is_float($val)){
      throw exceptions\InvalidArgumentException::getTypeException('float', gettype($val));
    }
    parent::__construct($val);
  }

  /**
   * Метод выполняет верификацию числа в соответствии с маской.
   * @param string $mask Маска верификации
   * Аргумент имеет структуру: <типВалидации> <ключи валидации>.
   * Возможные значения аргумента:
   * - == <целоеЧисло> - равенство числа;
   * - != <целоеЧисло> - неравенство числа;
   * - > <целоеЧисло> - большее чем указанное число;
   * - >= <целоеЧисло> - не меньше указанного числа;
   * - < <целоеЧисло> - меньше чем указанное число;
   * - <= <целоеЧисло> - не более чем указанное число;
   * - [] <целоеЧисло> <целоеЧисло> - в интервале числе;
   * - () <целоеЧисло> <целоеЧисло> - на отрезке чисел;
   * - in <целоеЧисло> <целоеЧисло> ... - среди указанных чисел;
   * - !in <целоеЧисло> <целоеЧисло> ... - не среди указанных чисел.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return boolean true - если верификация пройдена, иначе - false.
   */
  public function verify($mask){
    exceptions\InvalidArgumentException::verifyType($mask, 'S');
    $options = explode(' ', $mask);
    $typeVerify = array_shift($options);
    switch($typeVerify){
      case '==':
        if($this->getVal() != $options[0]){
          return false;
        }
        break;
      case '!=':
        if($this->getVal() == $options[0]){
          return false;
        }
        break;
      case '>':
        if($this->getVal() <= $options[0]){
          return false;
        }
        break;
      case '>=':
        if($this->getVal() < $options[0]){
          return false;
        }
        break;
      case '<':
        if($this->getVal() >= $options[0]){
          return false;
        }
        break;
      case '<=':
        if($this->getVal() > $options[0]){
          return false;
        }
        break;
      case '[]':
        if(!($this->getVal() >= $options[0] && $this->getVal() <= $options[1])){
          return false;
        }
        break;
      case '()':
        if(!($this->getVal() > $options[0] && $this->getVal() < $options[1])){
          return false;
        }
        break;
      case 'in':
        if(array_search($this->val, $options) === false){
          return false;
        }
        break;
      case '!in':
        if(array_search($this->val, $options) !== false){
          return false;
        }
        break;
      default:
        throw exceptions\InvalidArgumentException::getValidException('==|!=|>|>=|<|<=|[]|()|in|!in', $typeVerify);
    }

    return true;
  }

  /**
   * Метод приводит число к указанному интервалу.
   * @param integer $min [optional] Минимально допустимое значение. Если null, то ограничения нет.
   * @param integer $max [optional] Максимально допустимое значение. Если null, то ограничения нет.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return self Результирующее число.
   */
  public function prevent($min = null, $max = null){
    exceptions\InvalidArgumentException::verifyType($min, 'fin');
    exceptions\InvalidArgumentException::verifyType($max, 'fin');
    if(!is_null($min) && $this->val < $min){
      return new Float($min);
    }
    if(!is_null($max) && $this->val > $max){
      return new Float($max);
    }

    return $this;
  }
}
