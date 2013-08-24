<?php
namespace PPHP\tools\classes\standard\baseType\exceptions;

use \PPHP\tools\classes\standard\baseType as baseType;

/**
 * Исключение, свидетельствующее о получении параметра недопустимого типа или невалидного значения.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\exceptions
 */
class InvalidArgumentException extends SemanticException{
  /**
   * Метод выполняет проверку типа параметра и генерирует исключение в случае несоответствия.
   * Метод не возвращает результатов, так как ожидается, что отсутствие исключения является положительным результатом работы метода.
   * @param mixed $actualData Проверяемый параметр.
   * @param string $assertType Ожидаемый тип. Возможные значения аргумента:
   * - n - null;
   * - s - строка;
   * - S - не пустая строка;
   * - i - целочисленное значение;
   * - f - дробное значение;
   * - b - логическое значение;
   * - a - массив;
   * - o - объект.
   * Допускаются комбинации этих опций. При несоответствии всем указанным комбинацием генерируется исключение.
   * @throws static Выбрасывается в случае несоответствия проверяемого значения типу или при получении недопустимого значения второго аргумента.
   */
  public static function verifyType($actualData, $assertType){
    $assertType = new baseType\String($assertType);
    $errs = [];
    foreach($assertType as $o){
      switch($o){
        case 'n':
          if(!is_null($actualData)){
            $errs[] = 'null';
          }
          break;
        case 's':
          if(!is_string($actualData)){
            $errs[] = 'string';
          }
          break;
        case 'S':
          if(!is_string($actualData) || $actualData === ''){
            $errs[] = 'non empty string';
          }
          break;
        case 'i':
          if(!is_integer($actualData)){
            $errs[] = 'integer';
          }
          break;
        case 'f':
          if(!is_float($actualData)){
            $errs[] = 'float';
          }
          break;
        case 'b':
          if(!is_bool($actualData)){
            $errs[] = 'boolean';
          }
          break;
        case 'a':
          if(!is_array($actualData)){
            $errs[] = 'array';
          }
          break;
        case 'o':
          if(!is_object($actualData)){
            $errs[] = 'object';
          }
          break;
        default:
          throw self::getValidException('n|s|S|i|f|b|a|o', $o);
      }
      if(count($errs) === $assertType->count()){
        throw self::getTypeException($errs, gettype($actualData));
      }
    }
  }

  /**
   * Метод выполняет валидацию параметра и генерирует исключение в случае несоответствия.
   * Метод не возвращает результатов, так как ожидается, что отсутствие исключения является положительным результатом работы метода.
   * @param mixed $param Проверяемый параметр.
   * @param string $mask Маска валидации.
   * Аргумент имеет структуру: <типДанных> <маскаВалидатора>
   * В качестве типа данных доступны следующие занчения:
   * - s - строковая валидациия в соответствии с валидатором baseType/String::verify;
   * - i - числовая валидация в соответствии с валидаторами baseType/Integer::verify или baseType/Float::verify;
   * - a - валидация массива в соответствии с валидатором baseType/Array::verify.
   * @throws InvalidArgumentException Выбрасывается в случае несоответствия проверяемого значения маске или при получении недопустимого значения второго аргумента.
   */
  public static function verifyVal($param, $mask){
    self::verifyType($mask, 'S');
    $typeVerify = substr($mask, 0, 1);
    $mask = substr($mask, 2);
    if($typeVerify == 's'){
      $param = new baseType\String($param);
    }
    elseif($typeVerify == 'i'){
      $param = (is_integer($param))? new baseType\Integer($param) : new baseType\Float($param);
    }
    elseif($typeVerify == 'a'){
      $param = new baseType\Arr($param);
    }
    else{
      throw InvalidArgumentException::getValidException('s|i|a', $mask);
    }
    if(!$param->verify($mask)){
      throw InvalidArgumentException::getValidException($mask, $param->getVal());
    }
  }

  /**
   * Метод возвращает объект данного класса, устанавливая ему сообщение о недопустимом типе параметра.
   * @param string|string[] $assertType Ожидаемый тип параметра в виде строки или массива. Во втором случае предполагается соответствие хотя бы одному из ожидаемых типов.
   * @param string $actualType Реальный тип параметра.
   * @param integer $code [optional] Код ошибки.
   * @param \Exception $previous [optional] Причина.
   * @return InvalidArgumentException Объект данного типа с предустановленным сообщением.
   */
  public static function getTypeException($assertType, $actualType, $code = 0, \Exception $previous = null){
    $assertType = (is_string($assertType))? $assertType : implode('|', $assertType);

    return new InvalidArgumentException('Недопустимый тип параметра. Ожидается [' . $assertType . '] вместо [' . $actualType . '].', $code, $previous);
  }

  /**
   * Метод возвращает объект данного класса, устанавливая ему сообщение о недопустимом значении параметра.
   * @param string $mask Маска верификации значения параметра. Здесь могут использоваться верификаторы регулярных выражений или логические операторы.
   * @param mixed $actualData Реальное значение параметра.
   * @param integer $code [optional] Код ошибки.
   * @param \Exception $previous [optional] Причина.
   * @return InvalidArgumentException Объект данного типа с предустановленным сообщением.
   */
  public static function getValidException($mask, $actualData, $code = 0, \Exception $previous = null){
    return new InvalidArgumentException('Недопустимое значение параметра. Ожидается соответствие маске [' . $mask . '] вместо [' . $actualData . '].', $code, $previous);
  }
}
