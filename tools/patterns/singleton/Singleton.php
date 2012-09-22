<?php
namespace PPHP\tools\patterns\singleton;

/**
 * Интерфейс определяет шаблон Одиночка
 */
interface Singleton{
  /**
   * Метод возвращает экземпляр данного класса
   * @static
   * @abstract
   * @return static
   */
  static public function getInstance();
}
