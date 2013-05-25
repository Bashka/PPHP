<?php
namespace PPHP\tools\patterns\io;

/**
 * Классическая реализация выходного потока данных.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\io
 */
abstract class OutStream implements Writer{
  /**
   * Указатель на выходной поток, с которым работает объект.
   * @var resource
   */
  protected $resource;

  /**
   * @param resource $resource Указатель на выходной поток.
   */
  function __construct($resource){
    $this->resource = $resource;
  }
}
