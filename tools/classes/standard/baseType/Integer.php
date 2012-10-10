<?php
namespace PPHP\tools\classes\standard\baseType;

/**
 * Класс-обертка служит для предоставления дополнительной логики целым числам.
 */
class Integer extends wrapper{
  protected static $type = 'integer';

  /**
   * Метод приводит переданные данные к типу обертки.
   * @param mixed $val Приводимые данные.
   * @return mixed Приведенные данные.
   */
  protected function transform($val){
    return (integer) $val;
  }


  /**
   * Метод определяет, является ли указанное значение целым числом.
   * @static
   * @param mixed $val
   * @return boolean true - если данные являются целым числом или могут быть приведены к типу integer без потери данных, иначе - false.
   */
  public function is($val){
    if(is_integer($val)){
      return true;
    }
    elseif(is_float($val) && !isset(explode(".", $val)[1])){
      return true;
    }
    elseif(is_bool($val)){
      return true;
    }
    elseif(is_string($val)){
      $strVal = new \PPHP\tools\classes\standard\baseType\String($val);
      if($strVal->match('/^-?[0-9]+(.0+)?$/u')){
        return true;
      }
    }
    return false;
  }

  /**
   * Метод определяет, является ли число четным.
   * @return boolean true - если число четное, иначе - false.
   */
  public function isEven(){
    return ($this->val % 2 == 0);
  }

  /**
   * Метод считает число разрядов. Если число отрицательное, ведущий минус не считается за разряд.
   * @return integer Число разрядов.
   */
  public function count(){
    $strVal = new \PPHP\tools\classes\standard\baseType\String(($this->val >= 0)? $this->val : abs($this->val));
    return $strVal->count();
  }

  /**
   * Метод выполняет верификацию числа в соответствии с указанными параметрами.
   * @param null|integer $min Минимально допустимое значение. Если null, то ограничения нет.
   * @param null|integer $max Максимально допустимое значение. Если null, то ограничения нет.
   * @param null|integer $maxLength Максимально допустимое число разрядов.
   * @return boolean true - если верификация пройдена, иначе - false.
   */
  public function verify($min = null, $max = null, $maxLength = null){
    if((!is_null($min) && $this->val < $min) || (!is_null($max) && $this->val > $max)){
      return false;
    }
    if(!is_null($maxLength) && $this->count() > $maxLength){
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
      return new \PPHP\tools\classes\standard\baseType\Integer($min);
    }
    if(!is_null($max) && $this->val > $max){
      return new \PPHP\tools\classes\standard\baseType\Integer($max);
    }
    return $this;
  }
}
