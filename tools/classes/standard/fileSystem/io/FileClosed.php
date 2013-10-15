<?php
namespace PPHP\tools\classes\standard\fileSystem\io;

use PPHP\tools\patterns\io as io;

/**
 * Реализация интерфейса Closed для файловых потоков.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\fileSystem\io
 */
trait FileClosed{
  /**
   * @var boolean Флаг доступности потока.
   */
  private $closed = false;

  /**
   * @prototype \PPHP\tools\patterns\io\Closed
   */
  public function close(){
    if($this->isClose()){
      return true;
    }
    $this->closed = fclose($this->resource);

    return $this->closed;
  }

  /**
   * @prototype \PPHP\tools\patterns\io\Closed
   */
  public function isClose(){
    return $this->closed;
  }
}
