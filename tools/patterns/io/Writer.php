<?php
namespace PPHP\tools\patterns\io;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Интерфейс определяет выходной поток.
 *
 * Поток, реализующий данный интерфейс представляет собой соединение с ресурсом, установленное для записи данных.
 * Данные в выходной поток записываются побайтово с возможностью обработки строк (массива байт).
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\io
 */
interface Writer{
  /**
   * Метод записывает байт или строку в поток.
   * @abstract
   *
   * @param string $data Записываемая строка.
   *
   * @throws IOException Выбрасывается в случае возникновения ошибки при записи в поток.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return integer Число реально записанных байт.
   */
  public function write($data);
}
