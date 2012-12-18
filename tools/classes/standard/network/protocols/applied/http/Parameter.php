<?php
namespace PPHP\tools\classes\standard\network\protocols\applied\http;

/**
 * Класс представляет параметр заголовка HTTP запроса.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\network\protocols\applied\http
 */
class Parameter implements \PPHP\tools\patterns\interpreter\Interpreter, \PPHP\tools\patterns\interpreter\Restorable{
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
   * @param string $string Исходная строка.
   * @param null|mixed $driver[optional] Данные для восстановления.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты строки.
   * @return mixed Результирующий объект.
   */
  public static function reestablish($string, $driver = null){
    if(strpos($string, ':') === false){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Недостаточно данных для формирования объекта.');
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
   * @param null|mixed $driver[optional] Данные, позволяющие изменить логику интерпретации объекта.
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
