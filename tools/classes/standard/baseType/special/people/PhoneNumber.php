<?php
namespace PPHP\tools\classes\standard\baseType\special\people;
use \PPHP\tools\classes\standard\baseType as baseType;
use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс-обертка служит для представления и верификации телефонных номеров.
 * Допустимый тип: символ + за которым следует числовая последовательность, за которой следует открывающая скобка, числовая последовательность и закрывающая скобка, за которой следует числовая последовательность.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\special\people
 */
class PhoneNumber extends baseType\Wrapper{
  /**
   * Регион.
   * @var string
   */
  protected $region;
  /**
   * Код города.
   * @var string
   */
  protected $code;
  /**
   * Номер.
   * @var string
   */
  protected $number;

  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return [
      '\+([1-9][0-9]*)\(([0-9]+)\)([0-9]+)'
    ];
  }

  /**
   * Метод восстанавливает объект из строки.
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws exceptions\StructureException Выбрасывается в случае, если исходная строка не отвечает требования структуры.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return static Результирующий объект.
   */
  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    $m = parent::reestablish($string);

    $o = new self($string);
    $o->region = $m[1];
    $o->code = $m[2];
    $o->number = $m[3];
    return $o;
  }

  public function getCode(){
    return $this->code;
  }

  public function getNumber(){
    return $this->number;
  }

  public function getRegion(){
    return $this->region;
  }
}
