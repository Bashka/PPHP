<?php
namespace PPHP\tools\classes\standard\baseType;
use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\interpreter as interpreter;

/**
 * Класс-обертка для различных типов данных, отвечающая за контроль типа и добавляющая функциональность элементарным типам данных языка.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType
 */
abstract class Wrapper implements interpreter\Restorable{
  use interpreter\TRestorable;
  /**
   * Оборачиваемое значение.
   * @var mixed
   */
  protected $val;

  /**
   * Метод возвращает текущее значение обертки.
   * @return mixed
   */
  public function getVal(){
    return $this->val;
  }

  /**
   * Конструктор должен быть переопределен в дочерних классах с целью осуществления контроля типа передаваемого параметра.
   * @param mixed $val Оборачиваемое значение.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct($val){
    $this->val = $val;
  }
}
