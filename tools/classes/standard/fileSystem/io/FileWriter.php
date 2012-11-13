<?php
namespace PPHP\tools\classes\standard\fileSystem\io;

/**
 * Класс представляет выходной поток в файл.
 */
class FileWriter implements \PPHP\tools\patterns\io\Writer, \PPHP\tools\patterns\io\SeekIO, \PPHP\tools\patterns\io\Closed{
use FileClosed, FileSeekIO;

  /**
   * Дескриптор файла.
   * @var resource
   */
  protected $descriptor;

  /**
   * @param \resource $descriptor Дескриптор файла.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException
   */
  public function __construct($descriptor){
    if($descriptor instanceof \resource){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('resource', $descriptor);
    }
    $this->descriptor = $descriptor;
  }

  /**
   * Метод записывает строку в поток.
   * @param string $data Записываемая строка.
   * @return integer Число реально записанных байт.
   */
  public function write($data){
    return fwrite($this->descriptor, $data);
  }

  /**
   * Метод отчищает файл.
   * @return bool true - в случае устеха, иначе - false.
   */
  public function clean(){
    if(!ftruncate($this->descriptor, 0)){
      return false;
    }
    $this->setPosition(0);
    return true;
  }
}
