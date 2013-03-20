<?php
namespace PPHP\tools\classes\standard\baseType\special\network;
use \PPHP\tools\classes\standard\baseType as baseType;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс-обертка служит для представления и верификации номеров портов (TCP/IP).
 * Допустимый тип: целое число в диапазоне от 0 до 65536
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\special\network
 */
class Port extends baseType\wrapper{
  /**
   * Тип данной обертки.
   * @var string
   */
  protected static $type = 'port';

  /**
   * Метод приводит переданные данные к типу обертки.
   * @param mixed $val Приводимые данные.
   * @return mixed Приведенные данные.
   */
  protected function transform($val){
    return (integer)$val;
  }

  /**
   * Метод определяет, является ли указанное значение допустимым типом.
   * @static
   * @param mixed $val Проверяемое значение.
   * @return boolean true - если данные являются допустимым типом или могут быть приведены к нему без потери данных, иначе - false.
   */
  public static function is($val){
    try{
      $intVal = new baseType\Integer($val);
    }
    catch(exceptions\InvalidArgumentException $exc){
      return false;
    }

    $intVal = $intVal->getVal();
    if($intVal >= 0 && $intVal <= 65536){
      return true;
    }
    return false;
  }
}
