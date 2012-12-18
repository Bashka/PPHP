<?php
namespace PPHP\tools\classes\standard\network\protocols\applied\http;

/**
 * Класс представляет заголовок HTTP запроса.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\network\protocols\applied\http
 */
class Header implements \PPHP\tools\patterns\interpreter\Interpreter, \PPHP\tools\patterns\interpreter\Restorable{
  /**
   * Используемые в заголовке параметры.
   * @var Parameter[]
   */
  protected $parameters = [];

  /**
   * Метод восстанавливает объект из строки.
   * @param string $string Исходная строка.
   * @param null|mixed $driver[optional] Данные для восстановления.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты строки.
   * @return mixed Результирующий объект.
   */
  public static function reestablish($string, $driver = null){
    $string = explode($driver, $string);
    $resultObj = new static;
    foreach($string as $parameter){
      if($parameter !== ''){
        $parameter = Parameter::reestablish($parameter);
        $resultObj->addParameter($parameter);
      }
    }
    return $resultObj;
  }

  /**
   * Метод добавляет указанный параметр в заголовок.
   * @param Parameter $component Добавляемый параметр.
   */
  public function addParameter(Parameter $component){
    $this->parameters[$component->getName()] = $component;
  }

  /**
   * Метод создает из строки и добавляет указанный параметр в заголовок.
   * @param string $name Имя параметра.
   * @param string $value Значение параметра.
   */
  public function addParameterStr($name, $value){
    $this->parameters[$name] = new Parameter($name, $value);
  }

  /**
   * Метод определяет, присуствует ли указанный параметр заголовка.
   * @param string $name Имя параметра.
   * @return boolean true - если параметр присуствует, иначе - false.
   */
  public function hasParameter($name){
    return array_key_exists($name, $this->parameters);
  }

  /**
   * Метод возвращает строку, полученную при интерпретации объекта.
   * @param null|mixed $driver[optional] Данные, позволяющие изменить логику интерпретации объекта.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver = null){
    if(count($this->parameters) == 0){
      return $driver;
    }
    $result = '';
    foreach($this->parameters as $parameter){
      $result .= $parameter->interpretation().$driver;
    }
    return $result;
  }

  /**
   * Метод возрващает все указанные в данном заголовке параметры.
   * @return Parameter[]
   */
  public function getParameters(){
    return $this->parameters;
  }

  /**
   * Метод возвращает указанный параметр заголовка, если он задан.
   * @param string $name Имя целевого параметра.
   * @return boolean|Parameter Запрашиваемый параметр или false - если он не был задан.
   */
  public function getParameter($name){
    if(!$this->hasParameter($name)){
      return false;
    }
    return $this->parameters[$name];
  }

  /**
   * Метод возвращает значение указанного параметра заголовка.
   * @param string $name Целевой параметр.
   * @return boolean|string Значение целевого параметра заголовка или false - если он не был задан.
   */
  public function getParameterValue($name){
    $parameter = $this->getParameter($name);
    return ($parameter)? $parameter->getValue() : false;
  }
}
