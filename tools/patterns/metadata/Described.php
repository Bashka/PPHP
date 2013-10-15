<?php
namespace PPHP\tools\patterns\metadata;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс, реализующий данный интерфейс может быть описан с использованием метаданных.
 * Если объекты класса имеют устойчивое состояние, то аннотации могут быть сохранены между обращениями к объектам.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\metadata
 */
interface Described{
  /**
   * Метод возвращает все метаданные вызываемого объекта.
   * @abstract
   * @return string[]
   */
  public function getAllMetadata();

  /**
   * Метод возвращает значение конкретных метаданных вызываемого объекта.
   * @abstract
   * @param string $name Имя метаданных.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return string|null Метод возвращает значение метаданных или null, если метаданные не установлены.
   */
  public function getMetadata($name);

  /**
   * Метод устанавливает значение метаданных вызываемому объекту.
   * @abstract
   * @param string $name  Имя метаданных.
   * @param string $value Значение метаданных.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  public function setMetadata($name, $value);

  /**
   * Метод проверяет, установлены ли метаданные в вызываемом объекте.
   * @abstract
   * @param string $name Имя метаданных.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return boolean true - если метаданные установлены, иначе - false.
   */
  public function isMetadataExists($name);

  /**
   * Метод удаляет метаданные у вызываемого объекта.
   * @abstract
   * @param string $name Удаляемые метаданные.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  public function removeMetadata($name);
}
