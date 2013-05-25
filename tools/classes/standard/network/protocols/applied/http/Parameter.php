<?php
namespace PPHP\tools\classes\standard\network\protocols\applied\http;
use \PPHP\tools\patterns\interpreter as interpreter;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет параметр заголовка HTTP запроса.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\network\protocols\applied\http
 */
class Parameter extends interpreter\RestorableAdapter implements interpreter\Interpreter{
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
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return [
      '([A-Za-z0-9_\-]+):([^\n\r]*)'
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

    return new self($m[1], trim($m[2]));
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
