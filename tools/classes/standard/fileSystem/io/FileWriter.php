<?php
namespace PPHP\tools\classes\standard\fileSystem\io;
use \PPHP\tools\patterns\io as io;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет выходной поток в файл.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\fileSystem\io
 */
class FileWriter extends io\OutStream implements io\SeekIO, io\Closed{
use FileClosed, FileSeekIO;

  /**
   * Метод записывает байт или строку в поток.
   * @abstract
   *
   * @param string $data Записываемая строка.
   *
   * @throws io\IOException Выбрасывается в случае возникновения ошибки при записи в поток.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return integer Число реально записанных байт.
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
