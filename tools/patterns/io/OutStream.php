<?php
namespace PPHP\tools\patterns\io;

/**
 * Классическая реализация выходного потока данных.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\io
 */
abstract class OutStream implements Writer{
  /**
   * @var resource Указатель на выходной поток, с которым работает объект.
   */
  protected $resource;

  /**
   * @param resource $resource Указатель на выходной поток.
   */
  function __construct($resource){
    $this->resource = $resource;
  }
}
