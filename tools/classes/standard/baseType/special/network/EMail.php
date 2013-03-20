<?php
namespace PPHP\tools\classes\standard\baseType\special\network;
use \PPHP\tools\classes\standard\baseType as baseType;

/**
 * Класс-обертка служит для представления и верификации адресов электронной почты.
 * Допустимый тип: только латинские буквы, цифры, знак подчеркивания и тире, за которым следует знак @ за которым следует доменное имя.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\special\network
 */
class EMail extends baseType\wrapper{
  /**
   * Тип данной обертки.
   * @var string
   */
  protected static $type = 'EMail';

  /**
   * Локальное имя пользователя электронной почты.
   * @var string
   */
  protected $local;
  /**
   * Домен электронной почты.
   * @var DomainName
   */
  protected $domain;

  /**
   * Метод приводит переданные данные к типу обертки.
   * @param mixed $val Приводимые данные.
   * @return mixed Приведенные данные.
   */
  protected function transform($val){
    $val = explode('@', (string)$val);

    $this->local = $val[0];
    $this->domain = new DomainName($val[1]);
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
      $components = explode('@', $val);
      if(count($components) != 2 || !preg_match('/^[a-z0-9_-]+$/i', $components[0]) || !DomainName::is($components[1])){
        return false;
      }
      return true;
    }
    return false;
  }

  /**
   * @return DomainName
   */
  public function getDomain(){
    return $this->domain;
  }

  /**
   * @return string
   */
  public function getLocal(){
    return $this->local;
  }
}
