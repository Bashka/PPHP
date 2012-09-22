<?php
namespace PPHP\tools\patterns\metadata;

/**
 * Класс, реализующий данный интерфейс может быть описан с использованием метаданных.
 */
interface Described{
  /**
   * Метод возвращает все метаданные данного элемента.
   * @return \ArrayAccess
   */
  public function getAllMetadata();

  /**
   * Метод возвращает значение конкретных метаданных элемента.
   * @param $metadataName Имя метаданных.
   * @return string|null Метод возвращает значение метаданных или null, если метаданные не установлены.
   */
  public function getMetadata($metadataName);

  /**
   * Метод устанавливает значение метаданных.
   * @param string $metadataName Имя метаданных.
   * @param string $metadataValue Значение метаданных.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если аргумент имеет неверный тип.
   * @return void
   */
  public function setMetadata($metadataName, $metadataValue);

  /**
   * Метод проверяет, существуют ли заданные метаданные в вызываемом представлении.
   * @param $metadataName Имя метаданных.
   * @return boolean true - если метаданные существуют, иначе - false.
   */
  public function isMetadataExists($metadataName);

  /**
   * Метод удаляет метаданные из класса.
   * @abstract
   * @param string $metadataName Удаляемые метаданные.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если аргумент имеет неверный тип.
   */
  public function removeMetadata($metadataName);
}
