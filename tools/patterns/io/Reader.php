<?php
namespace PPHP\tools\patterns\io;

/**
 * Интерфейс определяет входной поток.
 *
 * Поток, реализующий данный интерфейс представляет собой соединение с ресурсом, установленное для чтения данных.
 * Данные из входного потока считываются посимвольно в цикле.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\io
 */
interface Reader{
  /**
   * Метод считывает один символ из потока.
   * @abstract
   * @return string|boolean Возвращает текущий символ из потока или false, если поток закончет.
   */
  public function read();
}
