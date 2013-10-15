<?php
namespace PPHP\tools\classes\standard\network\protocols\applied\http;

use \PPHP\tools\patterns\interpreter as interpreter;

/**
 * Класс представляет параметр заголовка HTTP запроса.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\network\protocols\applied\http
 */
class Parameter extends interpreter\RestorableAdapter implements interpreter\Interpreter{
  /**
   * @var string Имя параметра.
   */
  protected $name;

  /**
   * @var string Значение параметра.
   */
  protected $value;

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getMasks($driver = null){
    return ['([A-Za-z0-9_\-]+):([^\n\r]*)'];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Restorable
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
   * @prototype \PPHP\tools\patterns\interpreter\Interpreter
   */
  public function interpretation($driver = null){
    return $this->name . ':' . $this->value;
  }

  /**
   * Метод возвращает имя параметра.
   * @return string Имя параметра.
   */
  public function getName(){
    return $this->name;
  }

  /**
   * Метод возвращает значение параметра.
   * @return string Значение параметра.
   */
  public function getValue(){
    return $this->value;
  }
}
