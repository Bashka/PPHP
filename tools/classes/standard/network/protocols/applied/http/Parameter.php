<?php
namespace PPHP\tools\classes\standard\network\protocols\applied\http;
use \PPHP\tools\patterns\interpreter as interpreter;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет параметр заголовка HTTP запроса.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\network\protocols\applied\http
 */
class Parameter implements interpreter\Interpreter, interpreter\Restorable{
  /**
   * Имя параметра.
   * @var string
   */
  protected $name;
  /**
   * Значение параметра.
   * @var string
   */
  protected $value;

  /**
   * Метод восстанавливает объект из строки.
   * @abstract
   *
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   *
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты строки.
   * @throws exceptions\StructureException Выбрасывается в случае, если исходная строка не отвечает требования структуры.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return mixed Результирующий объект.
   */
  public static function reestablish($string, $driver = null){
    if(strpos($string, ':') === false){
      throw new exceptions\NotFoundDataException('Недостаточно данных для формирования объекта.');
    }
    $string = explode(':', $string);
    return new static($string[0], trim($string[1]));
  }

  function __construct($name, $value){
    $this->name = $name;
    $this->value = $value;
  }

  /**
   * Метод возвращает строку, полученную при интерпретации объекта.
   * @abstract
   *
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   *
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты объекта.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver = null){
    return $this->name.':'.$this->value;
  }

  /**
   * @return string
   */
  public function getName(){
    return $this->name;
  }

  /**
   * @return string
   */
  public function getValue(){
    return $this->value;
  }
}
