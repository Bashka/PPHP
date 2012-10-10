<?php
namespace PPHP\tools\classes\standard\baseType;

/**
 * Класс-обертка для различных типов данных, отвечающая за контроль типа и добавляющая функциональность элементарным типам данных языка.
 */
abstract class wrapper{
  /**
   * Оборачиваемое значение.
   * @var mixed
   */
  protected $val;

  /**
   * Тип данной обертки.
   * @var string
   */
  protected static $type;

  /**
   * Метод приводит переданные данные к типу обертки.
   * @abstract
   * @param mixed $val Приводимые данные.
   * @return mixed Приведенные данные.
   */
  protected abstract function transform($val);

  /**
   * Метод определяет, является ли указанное значение допустимым типом для данной обертки.
   * @static
   * @param mixed $val
   * @return boolean true - если данные являются допустимым типом или могут быть приведены к этому типу без потери данных, иначе - false.
   */
  public abstract function is($val);

  /**
   * Метод возвращает текущее значение обертки.
   * @return mixed
   */
  public function getVal(){
    return $this->val;
  }

  function __construct($val){
    if(!$this->is($val)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException(self::$type, $val);
    }
    $this->val = $this->transform($val);
  }
}
