<?php
namespace PPHP\tools\classes\standard\network\socket;
use \PPHP\tools\patterns\io as io;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет двунаправленный поток, используемый при сокетном соединении.
 *
 * Объекты данного класса могут быть использованы как входной и выходной поток к удаленному сокету.
 * Класс является фассадным и делегирует свои полномочия входному и выходному потоку в отдельности.
 * Закрытие либого из потоков (входного или выходного) приведет к закрытию парного потока (выходного и входного соответственно).
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\network\socket
 */
class Stream implements io\Closed, io\Reader, io\Writer{
  /**
   * Входной поток от удаленного сокета.
   * @var InStream
   */
  protected $in;
  /**
   * Выходной поток к удаленному сокету.
   * @var OutStream
   */
  protected $out;

  function __construct(InStream $in, OutStream $out){
    $this->in = $in;
    $this->out = $out;
  }

  /**
   * Метод закрывает поток.
   * @throws io\IOException Выбрасывается в случае невозможности закрытия сокетного потока из за ошибки.
   * @return boolean true - если поток удачно закрыт, иначе - false.
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
   * Метод проверяет, закрыт ли поток.
   * @return boolean true - если поток закрыт, иначе - false.
   */
  public function isClose(){
    return $this->in->isClose();
  }

  /**
   * Метод считывает один байт из потока.
   * @throws io\IOException Выбрасывается в случае возникновения ошибки при чтении из потока.
   * @return string|boolean Возвращает текущий байт из потока или false, если поток закончет.
   */
  public function read(){
    if(!$this->isClose()){
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
   * Метод считывает указанное количество байт из потока.
   *
   * @param integer $length Количество считываемых байт.
   *
   * @throws exceptions\InvalidArgumentException  Выбрасывается в случае возникновения ошибки при чтении из потока.
   * @throws io\IOException  Выбрасывается в случае возникновения ошибки при чтении из потока.
   * @return boolean|string Прочитанная строка или false - если достигнут конец потока.
   */
  public function readString($length){
    if(!$this->isClose()){
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
   * Метод считывает строку от текущей позиции до символа конца строки EOL.
   *
   * @param string $EOLSymbol [optional] Символ, принимаемый за EOL при данном вызове метода.
   *
   * @throws io\IOException Выбрасывается в случае возникновения ошибки при чтении из потока.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Прочитанная строка или пустая строка, если достигнут конец потока или символ EOL.
   */
  public function readLine($EOLSymbol = PHP_EOL){
    if(!$this->isClose()){
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
   * Метод считывает все содержимое потока.
   * @throws io\IOException Выбрасывается в случае возникновения ошибки при чтении из потока.
   * @return string Прочитанный массив символов или пустая строка, если достигнут конец потока.
   */
  public function readAll(){
    if(!$this->isClose()){
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
   * Метод записывает строку в поток.
   *
   * @param string $data Записываемая строка.
   *
   * @throws io\IOException Выбрасывается в случае возникновения ошибки при записи в поток.
   * @return integer Число реально записанных байт.
   */
  public function write($data){
    if(!$this->isClose()){
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
    if(!$this->isClose()){
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
