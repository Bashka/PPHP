<?php
namespace PPHP\tools\patterns\singleton;

use PPHP\tools\classes\standard\baseType\exceptions\RuntimeException;

/**
 * Классическая реализация интерфейса Singleton.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\singleton
 */
trait TSingleton{
  /**
   * @var array Множество экземпляров класса, относящихся к различным уровням иерархии наследования.
   */
  protected static $instance = [];

  /**
   * @prototype \PPHP\tools\patterns\singleton\Singleton
   */
  public final static function getInstance(){
    // Определение целевого класса в иерархии наследования.
    $calledClass = get_called_class();
    if(!isset(self::$instance[$calledClass])){
      self::$instance[$calledClass] = new static;
    }

    return self::$instance[$calledClass];
  }

  /**
   * Попытка клонирования приводит объекта данного класса приводит к выбросу исключения.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException Выбрасывается при вызове метода.
   */
  final function __clone(){
    throw new RuntimeException('Невозможно клонировать класс, реализующий интерфейс Singleton');
  }

  /**
   * Конструктор класса закрыт для использования.
   */
  private function __construct(){
  }
}
