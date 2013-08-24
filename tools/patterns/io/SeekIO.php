<?php
namespace PPHP\tools\patterns\io;

/**
 * Интерфейс определяет поток, содержание которого упорядочено.
 * Такой поток может смещать указатель текущего считываемого или записываемого байта, что позволяет получать данные от ресурса не последовательно.
 * Отсчет байтов в таком потоке начинается с нуля.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\io
 */
interface SeekIO{
  /**
   * Метод устанавливает указатель текущего байта.
   * @abstract
   * @param integer $position Позиция указателя текущего байта.
   * @throws \PPHP\tools\patterns\io\IOException Выбрасывается в случае ошибки при работе с потоком.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   */
  public function setPosition($position);

  /**
   * Метод возвращает позицию указателя текущего байта.
   * @abstract
   * @throws \PPHP\tools\patterns\io\IOException Выбрасывается в случае ошибки при работе с потоком.
   * @return integer Текущая позиция указателя.
   */
  public function getPosition();
}
