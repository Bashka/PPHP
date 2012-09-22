<?php
namespace PPHP\services\log;

/**
 * Представление сообщения журнала в системе.
 */
class Message{
  /**
   * Содержание сообщения.
   * @var string
   */
  protected $content;
  /**
   * Тип сообщения.
   * @var string
   */
  protected $type;
  /**
   * Дата создания сообщения.
   * @var string
   */
  protected $date;
  /**
   * Исключение.
   * @var \Exception
   */
  protected $exception;

  /**
   * Метод формирует сообщений журнала типа Info.
   * @static
   * @param string $content Содержание сообщения.
   * @return Message
   */
  public static function createInfo($content){
    return new static($content, LogManager::INFO);
  }

  /**
   * Метод формирует сообщений журнала типа Warning.
   * @static
   * @param string $content Содержание сообщения.
   * @param \Exception|null $exception Исклюлчение - основание.
   * @return Message
   */
  public static function createWarning($content, \Exception $exception=null){
    return new static($content, LogManager::WARNING, $exception);
  }

  /**
   * Метод формирует сообщений журнала типа Error.
   * @static
   * @param string $content Содержание сообщения.
   * @param \Exception $exception Исклюлчение - основание.
   * @return Message
   */
  public static function createError($content, \Exception $exception){
    return new static($content, LogManager::ERROR, $exception);
  }

  /**
   * @param string $content Содержание сообщения.
   * @param string $type Тип сообщения.
   * @param \Exception $exception Исклюлчение - основание.
   */
  private function __construct($content, $type=LogManager::ERROR, \Exception $exception=null){
    $this->date = date('d.m.Y', time());
    $this->content = $content;
    $this->type = $type;
    $this->exception = $exception;
  }

  /**
   * @return string
   */
  public function getType(){
    return $this->type;
  }

  /**
   * @return null
   */
  public function getException(){
    return $this->exception;
  }

  /**
   * @return mixed
   */
  public function getContent(){
    return $this->content;
  }

  /**
   * @return string
   */
  public function getDate(){
    return $this->date;
  }

  /**
   * Метод формирует строку сообщения для журнализации.
   * @return string
   */
  public function serialize(){
    $result = $this->type.'['.$this->date.']: '.$this->content;
    if(!empty($this->exception)){
      $result .= get_class($this->exception).'['.$this->exception->getFile().' - '.$this->exception->getLine().']: '.$this->exception->getMessage()."\n";
    }
    return $result;
  }
}
