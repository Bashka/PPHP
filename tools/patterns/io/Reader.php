<?php
namespace PPHP\tools\patterns\io;

/**
 * Интерфейс определяет входной поток.
 */
interface Reader{
  /**
   * Метод считывает один символ из потока.
   * @abstract
   * @return string|boolean Возвращает текущий символ из потока или false, если поток закончет.
   */
  public function read();
}
