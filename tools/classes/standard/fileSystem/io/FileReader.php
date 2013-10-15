<?php
namespace PPHP\tools\classes\standard\fileSystem\io;

use PPHP\tools\patterns\io as io;

/**
 * Класс представляет входной поток из файла.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\fileSystem\io
 */
class FileReader extends io\InStream implements io\SeekIO, io\Closed{
  use FileSeekIO, FileClosed;

  /**
   * @prototype \PPHP\tools\patterns\io\Reader
   */
  public function read(){
    $result = fread($this->resource, 1);
    if($result === false){
      throw new io\IOException('Ошибка использования потока ввода.');
    }

    return $result;
  }
}
