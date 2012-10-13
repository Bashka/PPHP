<?php
namespace PPHP\tools\classes\standard\baseType\special\network;

/**
 * Класс-обертка служит для представления и верификации номеров портов (TCP/IP).
 * Допустимый тип: целое число в диапазоне от 0 до 65536
 *
 */
class Port extends \PPHP\tools\classes\standard\baseType\wrapper{
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
   * @param mixed $val
   * @return boolean true - если данные являются допустимым типом или могут быть приведены к нему без потери данных, иначе - false.
   */
  public static function is($val){
    try{
      $intVal = new \PPHP\tools\classes\standard\baseType\Integer($val);
    }
    catch(\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException $exc){
      return false;
    }

    $intVal = $intVal->getVal();
    if($intVal >= 0 && $intVal <= 65536){
      return true;
    }
    return false;
  }
}
