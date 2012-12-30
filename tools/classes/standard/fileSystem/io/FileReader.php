<?php
namespace PPHP\tools\classes\standard\fileSystem\io;

/**
 * Класс представляет входной поток из файла.
 */
class FileReader extends \PPHP\tools\patterns\io\InStream implements \PPHP\tools\patterns\io\SeekIO, \PPHP\tools\patterns\io\Closed{
use FileSeekIO, FileClosed;

  /**
   * Метод считывает один символ из потока.
   * @throws \PPHP\tools\patterns\io\IOException Выбрасывается в случае возникновения ошибки при чтении из потока.
   * @return string|boolean Возвращает текущий символ из потока или false, если поток закончет.
   */
  public function read(){
    $result = fread($this->resource, 1);
    return ($result == '')? false : $result;
  }

  /**
   * Метод считывает заданное число байт из файла.
   * @deprecated
   * @param $length Число считываемых байт.
   * @return string Прочитанные символы.
   */
  public function readSet($length){
    return fread($this->resource, $length);
  }
}
