<?php
namespace PPHP\tools\patterns\io;

/**
 * Интерфейс определяет поток, содержание которого упорядочено.
 */
interface SeekIO{
  /**
   * Метод устанавливает указатель символа на указанную позицию.
   * @abstract
   * @param integer $position Позиция символа.
   * @return boolean true - если позиция установлена, иначе - false.
   */
  public function setPosition($position);

  /**
   * Метод возвращает текущую позицию указателя символа.
   * @abstract
   * @return integer
   */
  public function getPosition();
}
