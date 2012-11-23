<?php
namespace PPHP\tools\classes\standard\baseType\special\people;

/**
 * Класс-обертка служит для представления и верификации телефонных номеров.
 * Допустимый тип: символ + за которым следует числовая последовательность, за которой следует открывающая скобка, числовая последовательность и закрывающая скобка, за которой следует числовая последовательность.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\special\people
 */
class PhoneNumber extends \PPHP\tools\classes\standard\baseType\wrapper{
  /**
   * Тип данной обертки.
   * @var string
   */
  protected static $type = 'phoneNumber';

  /**
   * Регион.
   * @var string
   */
  protected $region;
  /**
   * Код города.
   * @var string
   */
  protected $code;
  /**
   * Номер.
   * @var string
   */
  protected $number;

  /**
   * Метод приводит переданные данные к типу обертки.
   * @param mixed $val Приводимые данные.
   * @return mixed Приведенные данные.
   */
  protected function transform($val){
    $val = (string)$val;
    $components = [];
    preg_match('/^\+([0-9]+)\(([0-9]+)\)([0-9]+)$/i', $val, $components);
    $this->region = $components[1];
    $this->code = $components[2];
    $this->number = $components[3];
    return $val;
  }

  /**
   * Метод определяет, является ли указанное значение допустимым типом.
   * @static
   * @param mixed $val Проверяемое значение.
   * @return boolean true - если данные являются допустимым типом или могут быть приведены к нему без потери данных, иначе - false.
   */
  public static function is($val){
    if(is_string($val)){
      if(preg_match('/^\+[0-9]+\([0-9]+\)[0-9]+$/', $val)){
        return true;
      }
    }
    return false;
  }

  public function getCode(){
    return $this->code;
  }

  public function getNumber(){
    return $this->number;
  }

  public function getRegion(){
    return $this->region;
  }
}
