<?php
namespace PPHP\tools\patterns\io;

/**
 * Интерфейс определяет поток, содержание которого упорядочено.
 *
 * Поток, реализующий данный интерфейс, содержит данные, упорядоченные определенным образом.
 * Такой поток может смещать указатель текущего считываемого или записываемого символа, что позволяет получать данные от ресурса не последовательно.
 * Отсчет байтов в потоке начинается с нуля.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\io
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
   * Метод возвращает текущую позицию указателя.
   * @abstract
   * @return integer Текущая позиция указателя.
   */
  public function getPosition();
}
