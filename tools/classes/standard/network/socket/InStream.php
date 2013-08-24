<?php
namespace PPHP\tools\classes\standard\network\socket;

use \PPHP\tools\patterns\io as io;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Объекты данного класса представляют сокетное соединение в виде входного потока.
 * Класс использует открытое сокетное соединение для формирования потока ввода.
 * Закрытые потоки не могут быть использованы или октрыты повторно.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\network\socket
 */
class InStream extends io\InStream implements io\Closed{
  /**
   * Флаг готовности потока.
   * @var boolean true - если поток открыт, false - если закрыт.
   */
  protected $isClose = false;

  /**
   * Таймаут ожидания при чтении данных (сек).
   * @var integer [optional]
   */
  protected $readTimeout = 1;

  /**
   * Метод закрывает поток.
   * @throws io\IOException Выбрасывается в случае невозможности закрытия сокетного потока из за ошибки.
   * @return boolean true - если поток удачно закрыт, иначе - false.
   */
  public function close(){
    if($this->isClose()){
      return true;
    }
    else{
      if(socket_shutdown($this->resource) === false){
        $code = socket_last_error($this->resource);
        throw new io\IOException('Ошибка закрытия сокета.' . socket_strerror($code), $code);
      }
      if(socket_close($this->resource) === false){
        $code = socket_last_error($this->resource);
        throw new io\IOException('Ошибка закрытия сокета.' . socket_strerror($code), $code);
      }
      $this->isClose = true;

      return true;
    }
  }

  /**
   * Метод проверяет, закрыт ли поток.
   * @return boolean true - если поток закрыт, иначе - false.
   */
  public function isClose(){
    return $this->isClose;
  }

  /**
   * Метод считывает один байт из потока.
   * Учитывайте то, что данный метод использует задержку для определения окончания передачи текущего байта.
   * Используйте заранее известные пакеты данных и метод readPackage чтобы избежать потери данных при передаче.
   * @throws io\IOException Выбрасывается в случае невозможности считывания данных из потока.
   * @return string Возвращает текущий байт из потока или пустую строку, если поток закончет.
   */
  public function read(){
    socket_set_option($this->resource, SOL_SOCKET, SO_RCVTIMEO, ['sec' => $this->readTimeout, 'usec' => 1]);
    $char = socket_read($this->resource, 1);
    socket_set_block($this->resource); // Остановка выполнения до выполнения чтения из потока
    if($char === false){
      $code = socket_last_error($this->resource);
      // В случае превышения интервала ожидания, предполагается конец потока
      if($code == 11){
        return '';
      }
      throw new io\IOException('Невозможно выполнть чтение из потока (' . $code . ': ' . socket_strerror($code) . '). Возможно сокетное соединение было сброшено.');
    }
    else{
      return $char;
    }
  }

  /**
   * Метод выполняет блокирующее чтение пакета указанной длины.
   * Если в потоке недостаточно данных для чтения, процесс ожидает получения этих данных.
   * @param integer $length Размер пакета в байтах.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @throws io\IOException Выбрасывается в случае возникновения ошибки при чтении из поток.
   * @return string Прочитанная строка или пустая строка если нет данных для чтения.
   */
  public function readPackage($length){
    exceptions\InvalidArgumentException::verifyType($length, 'i');
    exceptions\InvalidArgumentException::verifyVal($length, 'i > 0');
    $char = socket_read($this->resource, $length);
    socket_set_block($this->resource); // Остановка выполнения до выполнения чтения из потока
    if($char === false){
      $code = socket_last_error($this->resource);
      throw new io\IOException('Невозможно выполнть чтение из потока (' . $code . ': ' . socket_strerror($code) . '). Возможно сокетное соединение было сброшено.');
    }
    else{
      return $char;
    }
  }

  public function setReadTimeout($readTimeout){
    $this->readTimeout = $readTimeout;
  }

  public function getReadTimeout(){
    return $this->readTimeout;
  }
}