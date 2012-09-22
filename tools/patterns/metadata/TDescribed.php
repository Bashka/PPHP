<?php
namespace PPHP\tools\patterns\metadata;

trait TDescribed{
  /**
   * Метаданные элемента.
   * @var array
   */
  protected $metadata = [];

  /**
   * Метод возвращает все метаданные данного элемента.
   * @return array
   */
  public function getAllMetadata(){
    return $this->metadata;
  }

  /**
   * Метод возвращает значение конкретных метаданных элемента.
   * @param $metadataName Имя метаданных.
   * @return string|null Метод возвращает значение метаданных или null, если метаданные не установлены.
   */
  public function getMetadata($metadataName){
    if(!array_key_exists($metadataName, $this->metadata)){
      return null;
    }
    return $this->metadata[$metadataName];
  }

  /**
   * Метод устанавливает значение метаданных.
   * @param string $metadataName Имя метаданных.
   * @param string $metadataValue Значение метаданных.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если аргумент имеет неверный тип.
   * @return void
   */
  public function setMetadata($metadataName, $metadataValue){
    if(!is_string($metadataValue) || empty($metadataName) || !is_string($metadataName)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $metadataName);
    }
    $this->metadata[$metadataName] = $metadataValue;
  }

  /**
   * Метод проверяет, существуют ли заданные метаданные в вызываемом представлении.
   * @param $metadataName Имя метаданных.
   * @return boolean true - если метаданные существуют, иначе - false.
   */
  public function isMetadataExists($metadataName){
    return array_key_exists($metadataName, $this->metadata);
  }

  /**
   * Метод удаляет метаданные из класса.
   * @abstract
   * @param string $metadataName Удаляемые метаданные.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если аргумент имеет неверный тип.
   */
  public function removeMetadata($metadataName){
    if(!is_string($metadataName) || empty($metadataName)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $metadataName);
    }
    if(isset($this->metadata[$metadataName])){
      unset($this->metadata[$metadataName]);
    }
  }
}
