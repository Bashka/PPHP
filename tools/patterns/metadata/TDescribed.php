<?php
namespace PPHP\tools\patterns\metadata;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Классическая реализация интерфейса Described.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\metadata
 */
trait TDescribed{
  /**
   * Метаданные элемента.
   * Ассоциативный массив, ключами которого являются имена метаданных.
   * @var string[]
   */
  protected $metadata = [];

  /**
   * Метод возвращает все метаданные данного объекта.
   * @return string[]
   */
  public function getAllMetadata(){
    return $this->metadata;
  }

  /**
   * Метод возвращает значение конкретных метаданных элемента.
   *
   * @param $metadataName Имя метаданных.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return string|null Метод возвращает значение метаданных или null, если метаданные не установлены.
   */
  public function getMetadata($metadataName){
    exceptions\InvalidArgumentException::verifyType($metadataName, 'S');

    if(!array_key_exists($metadataName, $this->metadata)){
      return null;
    }
    return $this->metadata[$metadataName];
  }

  /**
   * Метод устанавливает значение метаданных.
   *
   * @param string $metadataName  Имя метаданных.
   * @param string $metadataValue Значение метаданных.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  public function setMetadata($metadataName, $metadataValue){
    exceptions\InvalidArgumentException::verifyType($metadataName, 'S');
    exceptions\InvalidArgumentException::verifyType($metadataValue, 's');

    $this->metadata[$metadataName] = $metadataValue;
  }

  /**
   * Метод проверяет, существуют ли заданные метаданные в вызываемом представлении.
   *
   * @param $metadataName Имя метаданных.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return boolean true - если метаданные существуют, иначе - false.
   */
  public function isMetadataExists($metadataName){
    exceptions\InvalidArgumentException::verifyType($metadataName, 'S');

    return array_key_exists($metadataName, $this->metadata);
  }

  /**
   * Метод удаляет метаданные из класса.
   *
   * @param string $metadataName Удаляемые метаданные.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  public function removeMetadata($metadataName){
    exceptions\InvalidArgumentException::verifyType($metadataName, 'S');

    if(isset($this->metadata[$metadataName])){
      unset($this->metadata[$metadataName]);
    }
  }
}
