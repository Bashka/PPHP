<?php
namespace PPHP\tools\classes\standard\fileSystem\io;
use \PPHP\tools\patterns\io as io;

/**
 * Реализация интерфейса Closed для файловых потоков.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\fileSystem\io
 */
trait FileClosed{
  /**
   * Флаг открытости потока.
   * @var boolean
   */
  private $closed = false;

  /**
   * Метод закрывает данный поток.
   * @throws io\IOException Выбрасывается в случае невозможности закрытия потока.
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
   * @return boolean true - если поток закрыт, иначе - false.
   */
  public function isClose(){
    return $this->closed;
  }
}
