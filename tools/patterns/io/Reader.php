<?php
namespace PPHP\tools\patterns\io;

/**
 * Интерфейс определяет входной поток.
 *
 * Поток, реализующий данный интерфейс представляет собой соединение с ресурсом, установленное для чтения данных.
 * Данные из входного потока считываются побайтно в цикле.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\io
 */
interface Reader{
  /**
   * Метод считывает один байт из потока.
   * @abstract
   * @throws \PPHP\tools\patterns\io\IOException Выбрасывается в случае возникновения ошибки при чтении из потока.
   * @return string|boolean Возвращает текущий байт из потока или false, если поток закончет.
   */
  public function read();

  /**
   * Метод считывает указанное количество байт из потока.
   * @param integer $length Количество считываемых байт.
   * @return boolean|string Прочитанная строка или false - если достигнут конец потока.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException  Выбрасывается в случае возникновения ошибки при чтении из потока.
   */
  public function readString($length);

  /**
   * Метод считывает строку от текущей позиции до символа конца строки EOL.
   * @return boolean|string Прочитанная строка или false - если достигнут конец потока (для данной функции концом потока так же являются символы \n и \r).
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае возникновения ошибки при чтении из потока.
   */
  public function readLine();

  /**
   * Метод считывает все содержимое потока.
   * @return boolean|string Прочитанный массив символов или false - если достигнут конец потока.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае возникновения ошибки при чтении из потока.
   */
  public function readAll();
}
