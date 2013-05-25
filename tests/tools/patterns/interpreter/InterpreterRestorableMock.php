<?php
namespace PPHP\tests\tools\patterns\interpreter;
use PPHP\tools\patterns\interpreter\Interpreter;
use PPHP\tools\patterns\interpreter\Restorable;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

class InterpreterRestorableMock implements Interpreter, Restorable{
  protected $var = 1;

  public function setVar($var){
    $this->var = $var;
  }

  public function getVar(){
    return $this->var;
  }

  /**
   * Метод возвращает строку, полученную при интерпретации объекта.
   *
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   *
   * @return string Результат интерпретации.
   */
  public function interpretation($driver = null){
    return 'InterpreterRestorableMock:'.$this->var;
  }

  /**
   * Метод позволяет определить допустимость интерпретации исходной строки в объект.
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return boolean true - если интерпретация возможна, иначе - false.
   */
  public static function isReestablish($string, $driver = null){
    return (boolean) preg_match('/[a-z0-9]+:[0-9]+/ui', $string);
  }

  /**
   * Метод восстанавливает объект из строки.
   *
   * @param string $string Исходная строка.
   * @param mixed  $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   *
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты строки.
   * @throws exceptions\StructureException Выбрасывается в случае, если исходная строка не отвечает требования структуры.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return static Результирующий объект.
   */
  public static function reestablish($string, $driver = null){
    $components = explode(':', $string);
    if(!isset($components[1])){
      throw new exceptions\NotFoundDataException;
    }
    $object = new self;
    $object->setVar($components[1]);
    return $object;
  }
}