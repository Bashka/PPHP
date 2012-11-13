<?php
namespace PPHP\tools\classes\standard\fileSystem\io;

/**
 * Класс представляет входной поток из файла.
 */
class FileReader implements \PPHP\tools\patterns\io\Reader, \PPHP\tools\patterns\io\SeekIO, \PPHP\tools\patterns\io\Closed{
use FileSeekIO, FileClosed;

  /**
   * Дескриптор файла.
   * @var \resource
   */
  protected $descriptor;

  /**
   * @param \resource $descriptor Дескриптор файла.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException
   */
  public function __construct($descriptor){
    if($descriptor instanceof \resource){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('resourse', $descriptor);
    }
    $this->descriptor = $descriptor;
  }

  /**
   * Метод считывает один символ из потока.
   * @return string|boolean Возвращает текущий символ из потока или false, если поток закончет.
   */
  public function read(){
    $result = fread($this->descriptor, 1);
    return ($result == '')? false : $result;
  }

  /**
   * Метод считывает всю текущую строку от текущей позиции до символа перевода строки.
   * @return string|boolean Текущая строка символов или false - если в потоке нет строк для считывания.
   */
  public function readLine(){
    $line = fgets($this->descriptor);
    if(is_bool($line)){
      $line = false;
    }
    else{
      $lengthLine = strlen($line);
      if($line[$lengthLine - 1] == "\n"){
        $line = substr($line, 0, $lengthLine - 1);
      }
    }
    return $line;
  }

  /**
   * Метод считывает заданное число байт из файла.
   * @param $length Число считываемых байт.
   * @return string Прочитанные символы.
   */
  public function readSet($length){
    return fread($this->descriptor, $length);
  }

  /**
   * Метод считывает все содержимое файла
   * @return string Содержимое файла
   */
  public function readAll(){
    $this->setPosition(0);
    $content = '';
    while(($char = $this->read()) !== false){
      $content .= $char;
    }
    return $content;
  }
}
