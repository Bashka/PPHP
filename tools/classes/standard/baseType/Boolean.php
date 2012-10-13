<?php
namespace PPHP\tools\classes\standard\baseType;

/**
 * Класс-обертка служит для верификации и представления логических данных в системе.
 */
class Boolean extends wrapper{
  protected static $type = 'boolean';

  /**
   * Метод приводит переданные данные к типу обертки.
   * @param mixed $val Приводимые данные.
   * @return mixed Приведенные данные.
   */
  protected function transform($val){
    if($val === 'false'){
      return false;
    }
    return (boolean) $val;
  }


  /**
   * Метод определяет, является ли указанное значение допустимым типом.
   * @static
   * @param mixed $val
   * @return boolean true - если данные являются допустимым типом или могут быть приведены к нему без потери данных, иначе - false.
   */
  public static function is($val){
    if(is_bool($val)){
      return true;
    }
    if($val === 1 || $val === 0 || $val === 'true' || $val === 'false'){
      return true;
    }
    return false;
  }
}
