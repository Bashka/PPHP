<?php
namespace PPHP\tools\classes\standard\baseType;

/**
 * Класс-обертка служит для предоставления дополнительной логики дробным числам.
 */
class Float extends wrapper{
  protected static $type = 'float';

  /**
   * Метод приводит переданные данные к типу обертки.
   * @param mixed $val Приводимые данные.
   * @return mixed Приведенные данные.
   */
  protected function transform($val){
    return (float) $val;
  }


  /**
   * Метод определяет, является ли указанное значение дробным числом.
   * @static
   * @param mixed $val
   * @return boolean true - если данные являются дробным числом или могут быть приведены к типу float без потери данных, иначе - false.
   */
  public function is($val){
    if(is_integer($val) || is_float($val) || is_bool($val)){
      return true;
    }
    elseif(is_string($val)){
      $strVal = new \PPHP\tools\classes\standard\baseType\String($val);
      if($strVal->match('/^-?[0-9]+(.[0-9]+)?$/u')){
        return true;
      }
    }
    return false;
  }

  /**
   * Метод выполняет верификацию числа в соответствии с указанными параметрами.
   * @param null|integer $min Минимально допустимое значение. Если null, то ограничения нет.
   * @param null|integer $max Максимально допустимое значение. Если null, то ограничения нет.
   * @return boolean true - если верификация пройдена, иначе - false.
   */
  public function verify($min = null, $max = null){
    if((!is_null($min) && $this->val < $min) || (!is_null($max) && $this->val > $max)){
      return false;
    }
    return true;
  }

  /**
   * Метод приводит число к указанному интервалу.
   * @param null|integer $min Минимально допустимое значение. Если null, то ограничения нет.
   * @param null|integer $max Максимально допустимое значение. Если null, то ограничения нет.
   * @return \PPHP\tools\classes\standard\baseType\Integer Результирующее число.
   */
  public function prevent($min = null, $max = null){
    if(!is_null($min) && $this->val < $min){
      return new \PPHP\tools\classes\standard\baseType\Float($min);
    }
    if(!is_null($max) && $this->val > $max){
      return new \PPHP\tools\classes\standard\baseType\Float($max);
    }
    return $this;
  }
}
