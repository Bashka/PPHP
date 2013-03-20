<?php
namespace PPHP\tools\classes\standard\baseType\special\fileSystem;
use \PPHP\tools\classes\standard\baseType as baseType;

/**
 * Класс-обертка служит для представления и верификации адресов файлов и папок в файловой системе.
 * Допустимый тип: любые символы кроме  : * ? " < > | \0 \ и без ведущего / символа, а так же без двух и более / символов, следующих один за другим.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\special\fileSystem
 */
class FileSystemAddress extends baseType\wrapper{
  /**
   * Тип данной обертки.
   * @var string
   */
  protected static $type = 'fileSystemAddress';

  /**
   * Флаг абсолютного адреса.
   * @var boolean true - если адрес абсолютный, иначе - false.
   */
  protected $isRoot;

  /**
   * Метод приводит переданные данные к типу обертки.
   * @param mixed $val Приводимые данные.
   * @return mixed Приведенные данные.
   */
  protected function transform($val){
    if(substr($val, 0, 1) == '/'){
      $this->isRoot = true;
    }
    else{
      $this->isRoot = false;
    }
    return (string) $val;
  }


  /**
   * Метод определяет, является ли указанное значение допустимым типом.
   * @static
   * @param mixed $val Проверяемое значение.
   * @return boolean true - если данные являются допустимым типом или могут быть приведены к нему без потери данных, иначе - false.
   */
  public static function is($val){
    if(is_string($val)){
      if(preg_match('/^[^:*?"<>\|\0\\\]+$/', $val)){
        if(!preg_match('/\/\//', $val)){
          return true;
        }
      }
    }
    return false;
  }

  /**
   * Метод определяет, является адрес абсолютным или относительным.
   * @return boolean true - если адрес абсолютный, иначе - false.
   */
  public function isRoot(){
    return $this->isRoot;
  }
}
