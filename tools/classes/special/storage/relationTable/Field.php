<?php
namespace PPHP\tools\classes\special\storage\relationTable;

/**
 * Представляет поле таблицы определенного типа. Предоставляет механизмы верификации и приведения данных к своему стандарту.
 */
abstract class Field{
  /**
   * Имя поля.
   * @var string
   */
  protected $name;

  /**
   * Обязательность поля.
   * @var boolean
   */
  protected $required;

  /**
   * Значение поля по умалчанию.
   * @var mixed
   */
  protected $default;

  /**
   * Метод верифицирует данные в соответствии с типом.
   * @abstract
   * @param mixed $data Верифицируемые данные.
   * @return boolean true - если данные не содержат отклонений от требований стандарта, иначе - false.
   */
  public abstract function verify($data = null);

  /**
   * Метод приводить данные к требуемому для записи формату.
   * @abstract
   * @param string $data Сериализуемые данные.
   * @return string Подготовленные для записи данные.
   */
  public abstract function serializeData($data = null);

  /**
   * Метод востанавливает данные из сериализованной ранее им строки.
   * @abstract
   * @param string $serializeData Сериализованные ранее данным классом данные.
   * @return null|mixed Востановленные данные.
   */
  public abstract function unserializeData($serializeData);

  /**
   * Метод возвращает длину данных после приведения.
   * @abstract
   * @return integer Длина данных для записи.
   */
  public abstract function getSizeData();

  /**
   * Метод проверяет, является ли данное поле обязательным.
   * @abstract
   * @return boolean true - если поле обязательно, иначе - false.
   */
  public function isRequired(){
    return $this->required;
  }

  /**
   * @param string $name Имя поля.
   * @param boolean $required Обязательность заполнения поля.
   * @param mixed|null $default Значение поля по умолчанию.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве имени поля передано значение, отличное от string типа.
   */
  function __construct($name, $required = false, $default = null){
    if(!is_string($name)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $name);
    }
    if(!is_bool($required)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('boolean', $required);
    }
    $this->name = $name;
    $this->required = $required;
    $this->default = $default;
  }

  /**
   * Метод возвращает имя вызываемого поля.
   * @return string Имя поля.
   */
  public function getName(){
    return $this->name;
  }

  /**
   * Метод возвращает размер поля после сериализации.
   * @return int Размер поля после сериализации.
   */
  public function getSize(){
    return strlen(serialize($this));
  }

  /**
   * Метод возвращает значение по умолчанию для данного поля.
   * @return mixed|null Значение по умолчанию.
   */
  public function getDefault(){
    return $this->default;
  }

  /**
   * Метод возвращает логический флаг, определяющий допустимость использования null в качестве значения.
   * @return boolean true - если поле обязательно должно иметь значение, иначе - false.
   */
  public function getRequired(){
    return $this->required;
  }
}
