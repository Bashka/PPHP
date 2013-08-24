<?php
namespace PPHP\tools\patterns\io;

use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;

/**
 * Интерфейс определяет входной поток.
 * Данные из входного потока считываются побайтно.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\io
 */
interface Reader{
  /**
   * Метод считывает один байт из потока.
   * @abstract
   * @throws \PPHP\tools\patterns\io\IOException Выбрасывается в случае возникновения ошибки при чтении из потока.
   * @return string Возвращает текущий байт из потока или пустую строку если поток закончет.
   */
  public function read();

  /**
   * Метод считывает указанное количество байт из потока.
   * @abstract
   * @param integer $length Количество считываемых байт.
   * @throws \PPHP\tools\patterns\io\IOException  Выбрасывается в случае возникновения ошибки при чтении из потока.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Если размер входного потока больше или равен размеру считываемой строки, то считывается строка указанного размера, если меньше, то оставшаяся в потоке строка, если в потоке нет данных возвращается пустая строка.
   */
  public function readString($length);

  /**
   * Метод считывает строку от текущей позиции до символа конца строки.
   * @abstract
   * @param string $EOLSymbol [optional] Символ, принимаемый за EOL.
   * @throws \PPHP\tools\patterns\io\IOException Выбрасывается в случае возникновения ошибки при чтении из потока.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Прочитанная строка или пустая строка, если достигнут конец потока или первым символом является EOL.
   */
  public function readLine($EOLSymbol = PHP_EOL);

  /**
   * Метод считывает все содержимое потока.
   * @abstract
   * @throws \PPHP\tools\patterns\io\IOException Выбрасывается в случае возникновения ошибки при чтении из потока.
   * @return string Символы, содержащиеся в потоке или пустая строка, если достигнут конец потока.
   */
  public function readAll();
}
