<?php
namespace PPHP\tools\patterns\io;

/**
 * Данный класс представляет классическую реализацию выходного потока данных.
 * Дочернему классу достаточно реализовать метод write, использующий определенный здесь указатель на ресурс.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\io
 */
abstract class OutStream implements Writer{
  /**
   * @var resource Указатель на ресурс, с которым работает объект.
   */
  protected $resource;

  /**
   * @param resource $resource Указатель на ресурс.
   */
  function __construct($resource){
    $this->resource = $resource;
  }
}
