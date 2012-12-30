<?php
namespace PPHP\tools\patterns\io;

/**
 * Представление выходного потока данных.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\io
 */
abstract class OutStream implements Writer{
  protected $resource;

  /**
   * @param resource $resource Указатель на входной поток.
   */
  function __construct($resource){
    $this->resource = $resource;
  }
}
