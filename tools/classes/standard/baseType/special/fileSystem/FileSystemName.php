<?php
namespace PPHP\tools\classes\standard\baseType\special\fileSystem;
use \PPHP\tools\classes\standard\baseType as baseType;

/**
 * Класс-обертка служит для представления и верификации имен файлов и папок.
 * Допустимый тип: любые символы кроме / : * ? " < > | \0 \
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\special\fileSystem
 */
class FileSystemName extends \PPHP\tools\classes\standard\baseType\wrapper{
  /**
   * Тип данной обертки.
   * @var string
   */
  protected static $type = 'fileSystemName';

  /**
   * Имя ресурса файловой системы.
   * @var string
   */
  protected $name;

  /**
   * Расширение ресурса файловой системы.
   * @var string
   */
  protected $expansion;

  /**
   * Метод приводит переданные данные к типу обертки.
   * @param mixed $val Приводимые данные.
   * @return mixed Приведенные данные.
   */
  protected function transform($val){
    $val = (string)$val;
    $strVal = new baseType\String($val);
    $positionPoint = $strVal->search('.', false, false);
    if($positionPoint > -1){
      $this->name = $strVal->subLeft($positionPoint)->getVal();
      $this->expansion = $strVal->subRight($strVal->length()-2-$positionPoint)->getVal();
    }
    else{
      $this->name = $val;
    }
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
      if(preg_match('/^[^\/:*?"<>\|\0\\\]+$/', $val)){
        return true;
      }
    }
    return false;
  }

  /**
   * @return string
   */
  public function getExpansion(){
    return $this->expansion;
  }

  /**
   * @return string
   */
  public function getName(){
    return $this->name;
  }
}
