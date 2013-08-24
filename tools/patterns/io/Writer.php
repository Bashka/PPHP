<?php
namespace PPHP\tools\patterns\io;

/**
 * Интерфейс определяет выходной поток.
 * Данные в выходной поток записываются побайтово и пакетно.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\io
 */
interface Writer{
  /**
   * Метод записывает байт или строку в поток.
   * @abstract
   * @param string $data Записываемый байт (строка).
   * @throws \PPHP\tools\patterns\io\IOException Выбрасывается в случае возникновения ошибки при записи в поток.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return integer Число реально записанных байт.
   */
  public function write($data);
}
