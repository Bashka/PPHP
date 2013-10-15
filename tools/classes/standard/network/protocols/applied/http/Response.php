<?php
namespace PPHP\tools\classes\standard\network\protocols\applied\http;

use \PPHP\tools\classes\standard\baseType as baseType;
use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;

/**
 * Класс представляет HTTP ответ сервера.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\network\protocols\applied\http
 */
class Response extends Message{
  /**
   * @var integer|string Код ответа.
   */
  protected $code;

  /**
   * @var string Сообщение ответа.
   */
  protected $message;

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getMasks($driver = null){
    if(is_null($driver)){
      $driver = "\r\n";
    }

    return ['HTTP\/1.1 ([0-9]{1,3}) ([A-Za-z ]+)' . $driver . '(' . Header::getMasks($driver)[0] . ')?' . $driver . '(.*)'];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Restorable
   */
  public static function reestablish($string, $driver = null){
    if(is_null($driver)){
      $driver = "\r\n";
    }
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    $m = parent::reestablish($string, $driver);
    $code = $m[1];
    $message = $m[2];
    $body = $m[6];
    if($body === ''){
      $body = null;
    }
    if($m[3] !== ''){
      $header = Header::reestablish($m[3]);
      if(!is_null($body)){
        if($header->hasParameter('Content-Length')){
          $body = substr($body, 0, (int) $header->getParameterValue('Content-Length'));
        }
      }
    }
    else{
      $header = null;
    }

    return new self($code, $message, $header, $body);
  }

  /**
   * @param integer|string $code Код ответа.
   * @param string $message Сообщение ответа.
   * @param \PPHP\tools\classes\standard\network\protocols\applied\http\Header $header [optional] Заголовок запроса.
   * @param string|array $body [optional] Тело запроса в виде строки или ассоциативного массива параметров, передаваемых в запросе. В случае передачи массива тело формируется следующим образом: <ключ элемента>:<значение элемента>EOL
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   */
  function __construct($code, $message, $header = null, $body = null){
    InvalidArgumentException::verifyType($code, 'iS');
    InvalidArgumentException::verifyType($message, 'S');
    parent::__construct($header, $body);
    $this->code = $code;
    $this->message = $message;
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Interpreter
   */
  public function interpretation($driver = null){
    if(is_null($driver)){
      $driver = "\r\n";
    }
    $generalHeader = 'HTTP/1.1 ' . $this->code . ' ' . $this->message;

    return $generalHeader . $driver . $this->header->interpretation($driver) . $driver . $this->body;
  }

  /**
   * Метод возвращает код ответа.
   * @return integer|string Код ответа.
   */
  public function getCode(){
    return $this->code;
  }

  /**
   * Метод вовзращает сообщение ответа.
   * @return string Сообщение ответа.
   */
  public function getMessage(){
    return $this->message;
  }
}
