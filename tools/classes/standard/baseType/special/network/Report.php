<?php
namespace PPHP\tools\classes\standard\baseType\special\network;
use \PPHP\tools\classes\standard\baseType as baseType;

/**
 * Класс-обертка служит для представления и верификации имен протоколов обмена данными.
 * Допустимый тип: имя одного из доступных протоколов за которым следует последовательность ://
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\special\network
 */
class Report extends \PPHP\tools\classes\standard\baseType\wrapper{
  /**
   * Тип данной обертки.
   * @var string
   */
  protected static $type = 'report';

  const HTTP = 'http';
  const HTTPS = 'https';
  const FTP = 'ftp';
  const DNS = 'dns';
  const SSH = 'ssh';
  const POP3 = 'pop3';
  const SMTP = 'smtp';

  /**
   * Имя протокола.
   * @var string
   */
  protected $name;

  /**
   * Метод приводит переданные данные к типу обертки.
   * @param mixed $val Приводимые данные.
   * @return mixed Приведенные данные.
   */
  protected function transform($val){
    $val = (string)$val;
    $strVal = new baseType\String($val);
    $this->name = strtolower($strVal->subLeft($strVal->search(':'))->getVal());
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
      if(preg_match('/^('.self::HTTP.'|'.self::HTTPS.'|'.self::FTP.'|'.self::DNS.'|'.self::SSH.'|'.self::POP3.'|'.self::SMTP.'):\/\/$/i', $val)){
        return true;
      }
    }
    return false;
  }

  public function getName(){
    return $this->name;
  }
}
