<?php
namespace PPHP\tools\classes\standard\fileSystem\io;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\io as io;

/**
 * Класс представляет выходной поток в файл.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\fileSystem\io
 */
class FileWriter extends io\OutStream implements io\SeekIO, io\Closed{
  use FileClosed, FileSeekIO;

  /**
   * @prototype \PPHP\tools\patterns\io\Writer
   */
  public function write($data){
    exceptions\InvalidArgumentException::verifyType($data, 'S');
    $result = fwrite($this->resource, $data);
    if($result === false){
      throw new io\IOException('Ошибка использования потока вывода.');
    }

    return $result;
  }

  /**
   * Метод отчищает содержимое потока (файл).
   * @return boolean true - в случае устеха, иначе - false.
   */
  public function clean(){
    if(!ftruncate($this->resource, 0)){
      return false;
    }
    $this->setPosition(0);

    return true;
  }
}
