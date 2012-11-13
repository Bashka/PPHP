<?php
namespace PPHP\tools\classes\special\storage\relationTable;

/**
 * Класс представляет поле типа Integer. Данное поле принимает данные только integer типа, количество разрядов в котором не превышает заданной длины.
 */
class IntegerField extends Field{
  /**
   * Максимальная разрядность числа.
   * @var integer
   */
  protected $maxLength;

  /**
   * Метод верифицирует данные в соответствии с типом.
   * @param mixed $data Верифицируемые данные.
   * @return boolean true - если данные не содержат отклонений от требований стандарта, иначе - false.
   */
  public function verify($data = null){
    $lenData = (is_null($data))? 0 : strlen((string) $data);
    if(!is_integer($data) && !is_null($data)){
      return false;
    }
    if($lenData > $this->maxLength){
      return false;
    }
    if(!is_null($this->default) && is_null($data)){
      $data = $this->default;
    }
    if($this->isRequired() && is_null($data)){
      return false;
    }
    return true;
  }

  /**
   * Метод приводить данные к требуемому для записи формату.
   * @param integer $data Сериализуемые данные.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве значения аргумента передан не string тип.
   * @return string Подготовленные для записи данные.
   */
  public function serializeData($data = null){
    if(!is_integer($data) && !is_null($data)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('integer', $data);
    }
    if(!is_null($this->default) && is_null($data)){
      $data = $this->default;
    }
    $data = (is_null($data))? '' : (string) $data;
    // Строка дополняется пробелами, если ее длины не достаточно
    if($this->maxLength > strlen($data)){
      $data = str_pad($data, $this->maxLength, ' ', STR_PAD_LEFT);
    }
    return $data;
  }

  /**
   * Метод востанавливает данные из сериализованной ранее им строки.
   * @param string $serializeData Сериализованные ранее данным классом данные.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве значения аргумента передан не string тип.
   * @return null|integer Востановленные данные.
   */
  public function unserializeData($serializeData){
    if(!is_string($serializeData)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $serializeData);
    }
    $trimData = trim($serializeData);
    if($trimData == ''){
      return null;
    }
    else{
      return ((int) $trimData);
    }
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
   * @param integer|null $default Значение поля по умолчанию.
   * @param $maxLength Максимальная разрядность числа.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве аргумента передано значение, отличное от integer типа.
   */
  function __construct($name, $maxLength, $required = false, $default = null){
    if(!is_integer($maxLength)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('integer', $maxLength);
    }
    if(!is_null($default) && !is_integer($default)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('integer', $default);
    }
    if(is_int($default) && strlen((string) $default) > $maxLength){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException();
    }
    parent::__construct($name, $required, $default);
    $this->maxLength = $maxLength;
  }
}
