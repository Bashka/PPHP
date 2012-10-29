<?php
namespace PPHP\tools\patterns\io;

/**
 * Интерфейс определяет выходной поток.
 *
 * Поток, реализующий данный интерфейс представляет собой соединение с ресурсом, установленное для записи данных.
 * Данные в выходной поток записываются как посимвольно, так и построчно.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\io
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
