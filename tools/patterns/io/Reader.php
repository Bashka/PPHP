<?php
namespace PPHP\tools\patterns\io;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Интерфейс определяет входной поток.
 *
 * Поток, реализующий данный интерфейс представляет собой соединение с ресурсом, установленное для чтения данных.
 * Данные из входного потока считываются побайтно в цикле.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\io
 */
interface Reader{
  /**
   * Метод считывает один байт из потока.
   * @abstract
   * @throws IOException Выбрасывается в случае возникновения ошибки при чтении из потока.
   * @return string Возвращает текущий байт из потока или пустую строку, если поток закончет.
   */
  public function read();

  /**
   * Метод считывает указанное количество байт из потока.
   * @abstract
   *
   * @param integer $length Количество считываемых байт.
   *
   * @throws IOException  Выбрасывается в случае возникновения ошибки при чтении из потока.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Если размер входного потока больше или равен размеру считываемой строки, то прочитанная строка указанного размера, если меньше, то оставшаяся в потоке строка, если в потоке нет данных - пустая строка.
   */
  public function readString($length);

  /**
   * Метод считывает строку от текущей позиции до символа конца строки EOL.
   *
   * @abstract
   *
   * @param string $EOLSymbol [optional] Символ, принимаемый за EOL при данном вызове метода.
   *
   * @throws IOException Выбрасывается в случае возникновения ошибки при чтении из потока.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Прочитанная строка или пустая строка, если достигнут конец потока или символ EOL.
   */
  public function readLine($EOLSymbol = PHP_EOL);

  /**
   * Метод считывает все содержимое потока.
   * @abstract
   * @throws IOException Выбрасывается в случае возникновения ошибки при чтении из потока.
   * @return string Прочитанный массив символов или пустая строка, если достигнут конец потока.
   */
  public function readAll();
}
