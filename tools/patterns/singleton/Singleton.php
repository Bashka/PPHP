<?php
namespace PPHP\tools\patterns\singleton;

/**
 * Интерфейс определяет шаблон Одиночка.
 *
 * Класс, реализующий данный интерфейс может быть инстанциирован только единожды, последующие попытки инстанциации приведут к возврату уже существующего экземпляра.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\singleton
 */
interface Singleton{
  /**
   * Метод возвращает экземпляр данного класса.
   * @static
   * @abstract
   * @return static
   */
  static public function getInstance();
}
