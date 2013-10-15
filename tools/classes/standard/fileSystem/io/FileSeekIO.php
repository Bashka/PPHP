<?php
namespace PPHP\tools\classes\standard\fileSystem\io;

use PPHP\tools\patterns\io as io;

/**
 * Реализация интерфейса SeekIO для файловых потоков.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\fileSystem\io
 */
trait FileSeekIO{
  /**
   * @prototype \PPHP\tools\patterns\io\SeekIO
   */
  public function setPosition($position){
    return (boolean) (fseek($this->resource, $position) + 1);
  }

  /**
   * @prototype \PPHP\tools\patterns\io\SeekIO
   */
  public function getPosition(){
    $result = ftell($this->resource);
    if($result === false){
      throw new io\IOException('Ошибка использования потока ввода.');
    }

    return $result;
  }
}
