<?php
namespace PPHP\tools\classes\standard\fileSystem\io;

/**
 * Класс представляет выходной поток в файл.
 */
class FileWriter extends \PPHP\tools\patterns\io\OutStream implements \PPHP\tools\patterns\io\SeekIO, \PPHP\tools\patterns\io\Closed{
use FileClosed, FileSeekIO;

  /**
   * Метод записывает строку в поток.
   * @param string $data Записываемая строка.
   * @throws \PPHP\tools\patterns\io\IOException Выбрасывается в случае возникновения ошибки при чтении из потока.
   * @return integer Число реально записанных байт.
   */
  public function write($data){
    return fwrite($this->resource, $data);
  }

  /**
   * Метод отчищает файл.
   * @return bool true - в случае устеха, иначе - false.
   */
  public function clean(){
    if(!ftruncate($this->resource, 0)){
      return false;
    }
    $this->setPosition(0);
    return true;
  }
}
