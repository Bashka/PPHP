<?php
namespace PPHP\tools\classes\standard\network\socket;

/**
 * Объекты данного класса представляют сокетное соединение в виде входного потока.
 *
 * Класс использует открытое сокетное соединение для формирования потока ввода.
 * Закрытые потоки не могут быть использованы или октрыты повторно.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\network\socket
 */
class InStream extends \PPHP\tools\patterns\io\InStream implements \PPHP\tools\patterns\io\Closed{
  /**
   * Флаг готовности потока.
   * @var boolean true - если поток открыт, false - если закрыт.
   */
  protected $isClose = false;

  /**
   * Таймаут ожидания при чтении данных.
   * @var integer[optional]
   */
  protected $readTimeout = 1;

  /**
   * Метод закрывает поток.
   * @throws \PPHP\tools\patterns\io\IOException Выбрасывается в случае невозможности закрытия сокета вызванного ошибкой.
   * @return boolean true - если поток удачно закрыт, иначе - false.
   */
  public function close(){
    if($this->isClose()){
      return true;
    }
    else{
      if(socket_shutdown($this->resource) === false){
        $code = socket_last_error($this->resource);
        throw new \PPHP\tools\patterns\io\IOException('Ошибка закрытия сокета.'.socket_strerror($code), $code);
      }
      if(socket_close($this->resource) === false){
        $code = socket_last_error($this->resource);
        throw new \PPHP\tools\patterns\io\IOException('Ошибка закрытия сокета.'.socket_strerror($code), $code);
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
   * @return string|boolean Возвращает текущий байт из потока или false, если поток закончет.
   */
  public function read(){
    socket_set_option($this->resource, SOL_SOCKET, SO_RCVTIMEO, ['sec' => $this->readTimeout, 'usec' => 1]);

    $char = socket_read($this->resource, 1);
    socket_set_block($this->resource);
    if($char === '' || $char === false){
      return false;
    }
    else{
      return $char;
    }
  }

  /**
   * Метод выполняет блокирующее чтение пакета указанной длины.
   * Если в потоке недостаточно данных для чтения, процесс ожидает получения этих данных.
   * @param integer $length Размер пакета в байтах.
   * @return string Прочитанная строка.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае указания пакета длиной менее 1 байта.
   * @throws \PPHP\tools\patterns\io\IOException Выбрасывается в случае возникновения ошибки при чтении из поток.
   */
  public function readPackage($length){
    if($length < 1){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('Ожидается длина большая 0');
    }
    socket_set_block($this->resource);
    $char = socket_read($this->resource, $length);
    if($char === false){
      $code = socket_last_error($this->resource);
      throw new \PPHP\tools\patterns\io\IOException(socket_strerror($code), $code);
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