<?php
namespace PPHP\tools\classes\standard\fileSystem\io;

/**
 * Реализация интерфейса Closed для файловых потоков.
 */
trait FileClosed{
  /**
   * Флаг открытости потока.
   * @var boolean
   */
  private $closed = false;

  /**
   * Метод закрывает данный поток.
   * @return boolean true - если поток закрыт, иначе - false.
   */
  public function close(){
    if($this->isClose()){
      return true;
    }
    $this->closed = fclose($this->resource);
    return $this->closed;
  }

  /**
   * Метод проверяет, закрыт ли поток.
   * @abstract
   * @return boolean true - если поток закрыт, иначе - false.
   */
  public function isClose(){
    return $this->closed;
  }
}
