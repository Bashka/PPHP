<?php
namespace PPHP\tools\patterns\io;

/**
 * Интерфейс определяет выходной поток.
 */
interface Writer{
  /**
   * Метод записывает строку в поток.
   * @abstract
   * @param string $data Записываемая строка.
   * @return integer Число реально записанных байт.
   */
  public function write($data);
}
