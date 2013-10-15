<?php
namespace PPHP\tools\classes\standard\network\socket;

use \PPHP\tools\patterns\io as io;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет двунаправленный поток, используемый при сокетном соединении.
 * Объекты данного класса могут быть использованы как входной и выходной поток к удаленному сокету.
 * Класс является фассадным и делегирует свои полномочия входному и выходному потоку в отдельности.
 * Закрытие либого из потоков (входного или выходного) приведет к закрытию парного потока (выходного и входного соответственно).
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\network\socket
 */
class Stream implements io\Closed, io\Reader, io\Writer{
  /**
   * @var \PPHP\tools\classes\standard\network\socket\InStream Входной поток от удаленного сокета.
   */
  protected $in;

  /**
   * @var \PPHP\tools\classes\standard\network\socket\OutStream Выходной поток к удаленному сокету.
   */
  protected $out;

  /**
   * @param \PPHP\tools\classes\standard\network\socket\InStream $in Входной поток.
   * @param \PPHP\tools\classes\standard\network\socket\OutStream $out Выходной поток.
   */
  function __construct(InStream $in, OutStream $out){
    $this->in = $in;
    $this->out = $out;
  }

  /**
   * @prototype \PPHP\tools\patterns\io\Closed
   */
  public function close(){
    try{
      return $this->in->close();
    }
    catch(io\IOException $e){
      throw $e;
    }
  }

  /**
   * @prototype \PPHP\tools\patterns\io\Closed
   */
  public function isClose(){
    return $this->in->isClose();
  }

  /**
   * @prototype \PPHP\tools\patterns\io\Reader
   */
  public function read(){
    if($this->isClose()){
      throw new io\IOException('Невозможно выполнить чтение из закрытого потока.');
    }
    try{
      return $this->in->read();
    }
    catch(io\IOException $e){
      throw $e;
    }
  }

  /**
   * @prototype \PPHP\tools\patterns\io\Reader
   */
  public function readString($length){
    if($this->isClose()){
      throw new io\IOException('Невозможно выполнить чтение из закрытого потока.');
    }
    try{
      return $this->in->readString($length);
    }
    catch(io\IOException $e){
      throw $e;
    }
    catch(exceptions\InvalidArgumentException $e){
      throw $e;
    }
  }

  /**
   * @prototype \PPHP\tools\patterns\io\Reader
   */
  public function readLine($EOLSymbol = PHP_EOL){
    if($this->isClose()){
      throw new io\IOException('Невозможно выполнить чтение из закрытого потока.');
    }
    try{
      return $this->in->readLine();
    }
    catch(io\IOException $e){
      throw $e;
    }
    catch(exceptions\InvalidArgumentException $e){
      throw $e;
    }
  }

  /**
   * @prototype \PPHP\tools\patterns\io\Reader
   */
  public function readAll(){
    if($this->isClose()){
      throw new io\IOException('Невозможно выполнить чтение из закрытого потока.');
    }
    try{
      return $this->in->readAll();
    }
    catch(io\IOException $e){
      throw $e;
    }
  }

  /**
   * @prototype \PPHP\tools\patterns\io\Writer
   */
  public function write($data){
    if($this->isClose()){
      throw new io\IOException('Невозможно выполнить запись в закрытый поток.');
    }
    try{
      return $this->out->write($data);
    }
    catch(io\IOException $e){
      throw $e;
    }
  }

  /**
   * Метод возвращает входной поток к удаленному сокету.
   * @return \PPHP\tools\classes\standard\network\socket\InStream Входной поток к удаленному сокету.
   */
  public function getIn(){
    return $this->in;
  }

  /**
   * Метод возвращает выходной поток к удаленному сокету.
   * @return \PPHP\tools\classes\standard\network\socket\OutStream Выходной поток к удаленному сокету.
   */
  public function getOut(){
    return $this->out;
  }

  /**
   * Метод устанавливает время блокировки ожидания данных при чтении.
   * @param integer $readTimeout Время блокировки ожидания данных при чтении в секундах.
   */
  public function setReadTimeout($readTimeout){
    if($this->isClose()){
      throw new io\IOException('Невозможно установить время блокировки для закрытого потока.');
    }
    $this->in->setReadTimeout($readTimeout);
  }

  /**
   * Метод возвращает время блокировки ожидания данных при чтении.
   * @return integer Время блокировки ожидания данных при чтении в секундах.
   */
  public function getReadTimeout(){
    return $this->in->getReadTimeout();
  }
}
