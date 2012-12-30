<?php
namespace PPHP\tools\classes\special\storage\relationTable;

class DateTimeField extends Field{
  const maxLength = 26;

  /**
   * Метод верифицирует данные в соответствии с типом.
   * @param mixed $data Верифицируемые данные.
   * @return boolean true - если данные не содержат отклонений от требований стандарта, иначе - false.
   */
  public function verify($data = null){
    if(!is_a($data, 'DateTime') && !is_null($data)){
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
   * @param \DateTime $data Сериализуемые данные.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве значения аргумента передан не DateTime тип.
   * @return string Подготовленные для записи данные.
   */
  public function serializeData($data = null){
    if(!is_a($data, '\DateTime') && !is_null($data)){
      throw new \InvalidArgumentException('DateTime', $data);
    }
    if(!is_null($this->default) && is_null($data)){
      $data = $this->default;
    }
    if(is_null($data)){
      return '                          ';
    }
    else{
      return str_pad($data->format('Y-m-d H:i:s'), self::maxLength, ' ', STR_PAD_LEFT);
    }
  }

  /**
   * Метод востанавливает данные из сериализованной ранее им строки.
   * @param string $serializeData Сериализованные ранее данным классом данные.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве значения аргумента передан не string тип.
   * @return null|\DateTime Востановленные данные.
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
      return \DateTime::createFromFormat('Y-m-d H:i:s', $trimData);
    }
  }

  /**
   * Метод возвращает длину данных после приведения.
   * @return integer Длина данных для записи.
   */
  public function getSizeData(){
    return self::maxLength;
  }

  /**
   * @param string $name Имя поля.
   * @param bool $required Обязательность заполнения поля.
   * @param \DateTime|null $default Значение поля по умолчанию.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве аргумента передано значение, отличное от DateTime типа.
   */
  function __construct($name, $required = false, $default = null){
    if(!is_null($default) && !is_a($default, '\DateTime')){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('DateTime', $default);
    }
    parent::__construct($name, $required, $default);
  }
}
