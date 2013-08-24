<?php
namespace PPHP\tools\patterns\metadata;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс, реализующий данный интерфейс может быть описан с использованием метаданных.
 * Реализация данного интерфейса свидетельствует о возможности аннотирования класса.
 * Если объекты класса имеют устойчивое состояние, то аннотации могут быть сохранены между обращениями к объектам.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\metadata
 */
interface Described{
  /**
   * Метод возвращает все метаданные данного объекта.
   * @abstract
   * @return \ArrayAccess
   */
  public function getAllMetadata();

  /**
   * Метод возвращает значение конкретных метаданных элемента.
   * @abstract
   * @param string $metadataName Имя метаданных.
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return string|null Метод возвращает значение метаданных или null, если метаданные не установлены.
   */
  public function getMetadata($metadataName);

  /**
   * Метод устанавливает значение метаданных.
   * @abstract
   * @param string $metadataName  Имя метаданных.
   * @param string $metadataValue Значение метаданных.
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  public function setMetadata($metadataName, $metadataValue);

  /**
   * Метод проверяет, существуют ли заданные метаданные в вызываемом представлении.
   * @abstract
   * @param string $metadataName Имя метаданных.
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return boolean true - если метаданные существуют, иначе - false.
   */
  public function isMetadataExists($metadataName);

  /**
   * Метод удаляет метаданные из класса.
   * @abstract
   * @param string $metadataName Удаляемые метаданные.
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  public function removeMetadata($metadataName);
}
