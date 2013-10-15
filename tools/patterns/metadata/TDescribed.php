<?php
namespace PPHP\tools\patterns\metadata;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Классическая реализация интерфейса Described.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\metadata
 */
trait TDescribed{
  /**
   * Метаданные объекта.
   * Ассоциативный массив, ключами которого являются имена метаданных.
   * @var string[]
   */
  protected $metadata = [];

  /**
   * @prototype \PPHP\tools\patterns\metadata\TDescribed
   */
  public function getAllMetadata(){
    return $this->metadata;
  }

  /**
   * @prototype \PPHP\tools\patterns\metadata\TDescribed
   */
  public function getMetadata($name){
    exceptions\InvalidArgumentException::verifyType($name, 'S');
    if(!array_key_exists($name, $this->metadata)){
      return null;
    }

    return $this->metadata[$name];
  }

  /**
   * @prototype \PPHP\tools\patterns\metadata\TDescribed
   */
  public function setMetadata($name, $value){
    exceptions\InvalidArgumentException::verifyType($name, 'S');
    exceptions\InvalidArgumentException::verifyType($value, 's');
    $this->metadata[$name] = $value;
  }

  /**
   * @prototype \PPHP\tools\patterns\metadata\TDescribed
   */
  public function isMetadataExists($name){
    exceptions\InvalidArgumentException::verifyType($name, 'S');

    return array_key_exists($name, $this->metadata);
  }

  /**
   * @prototype \PPHP\tools\patterns\metadata\TDescribed
   */
  public function removeMetadata($name){
    exceptions\InvalidArgumentException::verifyType($name, 'S');
    if(isset($this->metadata[$name])){
      unset($this->metadata[$name]);
    }
  }
}
