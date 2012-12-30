<?php
namespace PPHP\tools\classes\standard\baseType\special\network;

/**
 * Класс-обертка служит для представления и верификации IP-адреса 4 версии.
 * Допустимый тип: четыре цифры в диапазоне от 0 до 255 идущие подряд, разделеные точками.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\special\network
 */
class IPAddress4 extends \PPHP\tools\classes\standard\baseType\wrapper{
  /**
   * Тип данной обертки.
   * @var string
   */
  protected static $type = 'IPAddress';

  /**
   * Компоненты адреса.
   * @var integer[]
   */
  protected $trio = [0,0,0,0];

  /**
   * Метод приводит переданные данные к типу обертки.
   * @param mixed $val Приводимые данные.
   * @return mixed Приведенные данные.
   */
  protected function transform($val){
    $val = (string)$val;
    $this->trio = explode('.', $val);
    foreach($this->trio as &$v){
      $v = (integer)$v;
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
      $trio = [];
      if(preg_match('/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$/', $val, $trio)){
        for($i=1; $i<4; $i++){
          $trio[$i] = (integer)$trio[$i];
          if($trio[$i] < 0 || $trio[$i] > 255){
            return false;
          }
        }
        return true;
      }
    }
    return false;
  }

  /**
   * Метод возвращает указанное значение компонента адреса.
   * @param integer $index Индекс компонента в диапазоне от 0 до 3.
   * @return integer Значение компонента адреса.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\LogicException Выбрасывается в случае, если индек выходит за границы допустимого диапазона.
   */
  public function getTrio($index){
    if($index < 0 || $index > 3){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\LogicException('Недопустимый индекс массива.');
    }
    return $this->trio[$index];
  }

  /**
   * Метод возвращает значение компонента адреса в двоичной форме.
   * @param integer $index Индекс компонента в диапазоне от 0 до 3.
   * @return string Значение компонента адреса в двоичной форме.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\LogicException Выбрасывается в случае, если индек выходит за границы допустимого диапазона.
   */
  public function getTrioBin($index){
    return decbin($this->getTrio($index));
  }
}
