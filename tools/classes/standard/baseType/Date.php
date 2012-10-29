<?php
namespace PPHP\tools\classes\standard\baseType;

/**
 * Класс-обертка служит для предоставления дополнительной логики календарным числам.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType
 */
class Date extends wrapper{
  /**
   * Тип данной обертки.
   * @var string
   */
  protected static $type = 'date';

  /**
   * Метод приводит переданные данные к типу обертки.
   * @param mixed $val Приводимые данные.
   * @return mixed Приведенные данные.
   */
  protected function transform($val){
    return \DateTime::createFromFormat('d.m.Y', $val);
  }

  /**
   * Метод определяет, является ли указанное значение календарным числом.
   * @static
   * @param mixed $val Проверяемые данные.
   * @return boolean true - если данные являются календарным числом или могут быть приведены к типу Date без потери данных, иначе - false.
   */
  public static function is($val){
    if(preg_match('/^[0-3]?[0-9]\.[0-1]?[0-9]\.[0-9]+$/', $val)){
      return true;
    }
    return false;
  }

  /**
   * @return \DateTime
   */
  public function getDate(){
    return $this->val;
  }

  function __toString(){
    return $this->val->format('d.m.Y');
  }
}
