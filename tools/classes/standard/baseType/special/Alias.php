<?php
namespace PPHP\tools\classes\standard\baseType\special;
use \PPHP\tools\classes\standard\baseType as baseType;

/**
 * Класс-обертка служит для представления и верификации имен.
 * Допустимый тип: только латинские буквы любого регистра, пробел и цифры
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\special
 */
class Alias extends baseType\wrapper{
  /**
   * Тип данной обертки.
   * @var string
   */
  protected static $type = 'alias';

  /**
   * Метод приводит переданные данные к типу обертки.
   * @param mixed $val Приводимые данные.
   * @return mixed Приведенные данные.
   */
  protected function transform($val){
    return trim((string)$val);
  }


  /**
   * Метод определяет, является ли указанное значение допустимым типом.
   * @static
   * @param mixed $val Проверяемое значение.
   * @return boolean true - если данные являются допустимым типом или могут быть приведены к нему без потери данных, иначе - false.
   */
  public static function is($val){
    if(is_string($val)){
      if(preg_match('/^[A-Za-z0-9 ]+$/', $val)){
        return true;
      }
    }
    return false;
  }
}
