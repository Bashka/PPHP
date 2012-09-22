<?php
namespace PPHP\tools\classes\special\storage\relationTable;

/**
 * Класс представляет поле типа String. Данное поле принимает данные только string типа не превышающие по длине заданного размера.
 */
class StringField extends Field{
  /**
   * Максимальная длина строки.
   * @var integer
   */
  protected $maxLength;

  /**
   * Метод верифицирует данные в соответствии с типом.
   * @param mixed $data Верифицируемые данные.
   * @return boolean true - если данные не содержат отклонений от требований стандарта, иначе - false.
   */
  public function verify($data = null){
    $lenData = strlen((string) $data);
    if(!is_string($data) && !is_null($data)){
      return false;
    }
    if($lenData > $this->maxLength){
      return false;
    }
    if(!is_null($this->default) && (is_null($data) || $data == '')){
      $data = $this->default;
    }
    if($this->isRequired() && (is_null($data) || $data == '')){
      return false;
    }
    return true;
  }

  /**
   * Метод приводить данные к требуемому для записи формату.
   * @param string $data Сериализуемые данные.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве значения аргумента передан не string тип.
   * @return string Подготовленные для записи данные.
   */
  public function serializeData($data = null){
    if(!is_string($data) && !is_null($data)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $data);
    }
    if(!is_null($this->default) && (is_null($data) || $data == '')){
      $data = $this->default;
    }
    // Строка дополняется пробелами, если ее длины не достаточно
    if($this->maxLength > strlen($data)){
      $data = str_pad($data, $this->maxLength, ' ', STR_PAD_LEFT);
    }
    if(is_null($data)){
      return '';
    }
    return $data;
  }

  /**
   * Метод востанавливает данные из сериализованной ранее им строки.
   * @param string $serializeData Сериализованные ранее данным классом данные.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве значения аргумента передан не string тип.
   * @return null|string Востановленные данные.
   */
  public function unserializeData($serializeData){
    if(!is_string($serializeData)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $serializeData);
    }
    return trim($serializeData);
  }

  /**
   * Метод возвращает длину данных после приведения.
   * @return integer Длина данных для записи.
   */
  public function getSizeData(){
    return $this->maxLength;
  }

  /**
   * @param $name Имя поля.
   * @param bool $required Обязательность заполнения поля.
   * @param string|null $default Значение поля по умолчанию.
   * @param $maxLength Максимальная длина строки.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве аргумента передано значение, отличное от string типа.
   */
  function __construct($name, $maxLength, $required = false, $default = null){
    if(!is_integer($maxLength)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('integer', $maxLength);
    }
    if(!is_null($default) && !is_string($default)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $default);
    }
    if(is_string($default) && strlen($default) > $maxLength){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException();
    }
    parent::__construct($name, $required, $default);
    $this->maxLength = $maxLength;
  }
}
