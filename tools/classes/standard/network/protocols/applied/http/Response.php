<?php
namespace PPHP\tools\classes\standard\network\protocols\applied\http;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use \PPHP\tools\classes\standard\baseType as baseType;

/**
 * Класс представляет HTTP ответ сервера.
 *
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\network\protocols\applied\http
 */
class Response extends Message{
  /**
   * Код ответа.
   * @var string
   */
  protected $code;

  /**
   * Сообщение ответа.
   * @var string
   */
  protected $message;

  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    if(is_null($driver)){
      $driver = "\r\n";
    }
    return ['HTTP\/1.1 ([0-9]{1,3}) ([A-Za-z ]+)' . $driver . '(' . Header::getMasks($driver)[0] . ')?' . $driver . '(.*)'];
  }

  /**
   * Метод восстанавливает объект из строки.
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws exceptions\StructureException Выбрасывается в случае, если исходная строка не отвечает требования структуры.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return static Результирующий объект.
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
          $body = substr($body, 0, (int)$header->getParameterValue('Content-Length'));
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
   * @param Header $header [optional] Заголовок запроса.
   * @param string|array $body [optional] Тело запроса в виде строки или ассоциативного массива параметров, передаваемых в запросе. В случае передачи массива тело формируется следующим образом: <ключ элемента>:<значение элемента>EOL
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   */
  function __construct($code, $message, $header = null, $body = null){
    exceptions\InvalidArgumentException::verifyType($code, 'iS');
    exceptions\InvalidArgumentException::verifyType($message, 'S');
    parent::__construct($header, $body);
    $this->code = $code;
    $this->message = $message;
  }

  /**
   * Метод возвращает строку, полученную при интерпретации объекта.
   * @abstract
   *
   * @param mixed $driver [optional] Разделитель компонентов ответа. По умолчанию PHP_EOL.
   *
   * @return string Результат интерпретации.
   */
  public function interpretation($driver = null){
    if(is_null($driver)){
      $driver = "\r\n";
    }
    $generalHeader = 'HTTP/1.1 ' . $this->code . ' ' . $this->message;
    return $generalHeader . $driver . $this->header->interpretation($driver) . $driver . $this->body;
  }

  public function getCode(){
    return $this->code;
  }

  public function getMessage(){
    return $this->message;
  }
}
