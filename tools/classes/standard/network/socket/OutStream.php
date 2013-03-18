<?php
namespace PPHP\tools\classes\standard\network\socket;

/**
 * Объекты данного класса представляют сокетное соединение в виде выходного потока.
 *
 * Класс использует открытое сокетное соединение для формирования потока вывода.
 * Закрытые потоки не могут быть использованы или октрыты повторно.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\network\socket
 */
class OutStream extends \PPHP\tools\patterns\io\OutStream implements \PPHP\tools\patterns\io\Closed{
  /**
   * Флаг готовности потока.
   * @var boolean true - если поток открыт, false - если закрыт.
   */
  protected $isClose;

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
   * Метод записывает строку в поток.
   * @param string $data Записываемая строка.
   * @throws \PPHP\tools\patterns\io\IOException Выбрасывается в случае возникновения ошибки при записи в поток.
   * @return integer Число реально записанных байт.
   */
  public function write($data){
    $result = socket_write($this->resource, $data);
    if($result === false){
      $code = socket_last_error($this->resource);
      throw new \PPHP\tools\patterns\io\IOException(socket_strerror($code), $code);
    }
    else{
      return $result;
    }
  }
}
