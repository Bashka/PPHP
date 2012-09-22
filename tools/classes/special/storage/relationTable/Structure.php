<?php
namespace PPHP\tools\classes\special\storage\relationTable;

/**
 * Представляет структуру таблицы и обеспечивает работу с записями.
 */
class Structure{
  /**
   * Поля структуры.
   * @var \SplObjectStorage
   */
  private $fields;

  public function __construct(){
    $this->fields = new \SplObjectStorage();
  }

  /**
   * Метод добавляет новое поле в структуру.
   * @param Field $field Новое поле.
   */
  public function addField(Field $field){
    $this->fields->attach($field);
  }

  /**
   * Метод возвращает размер структуры после сериализации.
   * @return int Размер структуры после сериализации.
   */
  public function getSize(){
    return strlen(serialize($this));
  }

  /**
   * Метод возвращает суммарную длину записи.
   * @abstract
   * @return integer Длина данных для записи.
   */
  public function getSizeData(){
    $sumSize = 0;
    foreach($this->fields as $field){
      $sumSize += $field->getSizeData();
    }
    return $sumSize;
  }

  /**
   * Метод сериализует запись согласно установленным полям.
   * @param array $data Сериализуемая запись. Ключами элементов записи должны быть имена полей структуры.
   * @throws InvalidDataException Выбрасывается в случае, если какие либо данные записи не соответвтуют требованиям структуры.
   * @return string Сериализованная запись.
   */
  public function serializeData(array $data){
    $serializeData = '';
    foreach($this->fields as $field){
      $fieldName = $field->getName();
      $dataElm = (isset($data[$fieldName]))? $data[$fieldName] : null;
      if(!$field->verify($dataElm)){
        throw new InvalidDataException();
      }
      $serializeData .= $field->serializeData($dataElm);
    }
    return $serializeData;
  }

  /**
   * Востанавливает запись в массив согласно полям структуры.
   * @param string $serializeData Сериализованная запись.
   * @return array Массив востановленных данных.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве аргумента передано значение не string типа.
   */
  public function unserializeData($serializeData){
    if(!is_string($serializeData)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $serializeData);
    }
    $startPoint = 0;
    $result = [];
    foreach($this->fields as $field){
      $sizeData = $field->getSizeData();
      $dataField = substr($serializeData, $startPoint, $sizeData);
      $result[$field->getName()] = $field->unserializeData($dataField);
      $startPoint += $sizeData;
    }
    return $result;
  }

  public function getCountFields(){
    return $this->fields->count();
  }
}
