<?php
namespace PPHP\tools\classes\standard\network\socket;

/**
 * Класс представляет двунаправленный поток, используемый при сокетном соединении.
 *
 * Объекты данного класса могут быть использованы как входной и выходной поток к удаленному сокету.
 * Класс является фассадным и делегирует свои полномочия входному и выходному потоку в отдельности.
 * Закрытие либого из потоков (входного или выходного) приведет к закрытию парного потока (выходного и входного соответственно).
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\network\socket
 */
class Stream implements \PPHP\tools\patterns\io\Closed, \PPHP\tools\patterns\io\Reader, \PPHP\tools\patterns\io\Writer{
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
   * @return boolean true - если поток удачно закрыт, иначе - false.
   */
  public function close(){
    return $this->in->close();
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
   * @throws \PPHP\tools\patterns\io\IOException Выбрасывается в случае возникновения ошибки при чтении из потока.
   * @return string|boolean Возвращает текущий байт из потока или false, если поток закончет.
   */
  public function read(){
    return $this->in->read();
  }

  /**
   * Метод считывает указанное количество байт из потока.
   * @param integer $length Количество считываемых байт.
   * @return boolean|string Прочитанная строка или false - если достигнут конец потока.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException  Выбрасывается в случае возникновения ошибки при чтении из потока.
   */
  public function readString($length){
    return $this->in->readString($length);
  }

  /**
   * Метод считывает строку от текущей позиции до символа конца строки EOL.
   * @return boolean|string Прочитанная строка или false - если достигнут конец потока (для данной функции концом потока так же являются символы \n и \r).
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае возникновения ошибки при чтении из потока.
   */
  public function readLine(){
    return $this->in->readLine();
  }

  /**
   * Метод считывает все содержимое потока.
   * @return boolean|string Прочитанный массив символов или false - если достигнут конец потока.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае возникновения ошибки при чтении из потока.
   */
  public function readAll(){
    return $this->in->readAll();
  }

  /**
   * Метод записывает строку в поток.
   * @param string $data Записываемая строка.
   * @throws \PPHP\tools\patterns\io\IOException Выбрасывается в случае возникновения ошибки при записи в поток.
   * @return integer Число реально записанных байт.
   */
  public function write($data){
    return $this->out->write($data);
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
