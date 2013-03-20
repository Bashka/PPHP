<?php
namespace PPHP\tools\patterns\metadata;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Классическая реализация интерфейса PPHP\tools\patterns\metadata\Described.
 *
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\metadata
 */
trait TDescribed{
  /**
   * Метаданные элемента.
   * Ассоциативный массив, ключами которого являются имена методанных.
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
    if(is_string($metadataName) || empty($metadataName)){
      throw new exceptions\InvalidArgumentException('string', $metadataName);
    }

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
   * @return void
   */
  public function setMetadata($metadataName, $metadataValue){
    if(!is_string($metadataValue) || empty($metadataName) || !is_string($metadataName)){
      throw new exceptions\InvalidArgumentException('string', $metadataName);
    }
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
    if(is_string($metadataName) || empty($metadataName)){
      throw new exceptions\InvalidArgumentException('string', $metadataName);
    }

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
    if(!is_string($metadataName) || empty($metadataName)){
      throw new exceptions\InvalidArgumentException('string', $metadataName);
    }
    if(isset($this->metadata[$metadataName])){
      unset($this->metadata[$metadataName]);
    }
  }
}
