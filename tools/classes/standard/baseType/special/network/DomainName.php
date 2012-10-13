<?php
namespace PPHP\tools\classes\standard\baseType\special\network;

/**
 * Класс-обертка служит для представления и верификации доменных имен.
 * Допустимый тип: должно начинаться латинской буквой или цифрой, а заканчиваться буквой, цифрой или знаком тире. Может содержать точки, но не идущие подряд и обязательно обрамленые знаком тире, латинской буквой или цифрой.
 * /^[a-z0-9][a-z0-9-]*((\.[a-z0-9-]+)*|\.)[a-z0-9]$/i
 */
class DomainName extends \PPHP\tools\classes\standard\baseType\wrapper{
  protected static $type = 'domainName';

  /**
   * Компоненты адреса.
   * @var string[]
   */
  protected $subDomains = [];

  /**
   * Метод приводит переданные данные к типу обертки.
   * @param mixed $val Приводимые данные.
   * @return mixed Приведенные данные.
   */
  protected function transform($val){
    $val = (string)$val;
    $this->subDomains = array_reverse(explode('.', $val));
    return $val;
  }

  /**
   * Метод определяет, является ли указанное значение допустимым типом.
   * @static
   * @param mixed $val
   * @return boolean true - если данные являются допустимым типом или могут быть приведены к нему без потери данных, иначе - false.
   */
  public static function is($val){
    if(is_string($val)){
      if(preg_match('/^[a-z0-9][a-z0-9-]*((\.[a-z0-9-]+)*|\.)[a-z0-9]$/i', $val)){
        return true;
      }
    }
    return false;
  }

  /**
   * Метод возвращает указанное значение компонента адреса.
   * @param integer $index Индекс компонента в диапазоне от 0 до порядкового номера поддомена.
   * @return string Значение компонента адреса.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\LogicException Выбрасывается в случае, если индек выходит за границы допустимого диапазона.
   */
  public function getComponent($index){
    if($index < 0 || $index >= count($this->subDomains)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\LogicException('Недопустимый индекс массива.');
    }
    return $this->subDomains[$index];
  }
}
