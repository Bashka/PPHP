<?php
namespace PPHP\tools\classes\standard\network\protocols\applied\http;

/**
 * Класс представляет HTTP ответ сервера.
 * @author Artur Sh. Mamedbekov
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
   * Метод восстанавливает объект из строки.
   * @param string $string Исходная строка.
   * @param null|mixed $driver[optional] Данные для восстановления. Данный метод принимает символ конца строки для парсинга ответа.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты строки.
   * @return mixed Результирующий объект.
   */
  public static function reestablish($string, $driver = null){
    if($string == ''){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Отсутствуют данные для формирования объекта.');
    }
    $string = new \PPHP\tools\classes\standard\baseType\String($string);

    $generalHeader = $string->nextComponent($driver);
    if($generalHeader === false){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Отсутствуют данные для формирования объекта. Отсутствует стартовая строка ответа.');
    }
    $generalHeader->nextComponent(' ');
    $code = $generalHeader->nextComponent(' ')->getVal();
    if($code === false){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Отсутствуют данные для формирования объекта. Отсутствует данные о коде ответа.');
    }
    $message = $generalHeader->sub()->getVal();
    if($message === false){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Отсутствуют данные для формирования объекта. Отсутствует данные о сообщении ответа.');
    }

    $header = $string->nextComponent($driver.$driver);
    if($header === false){
      if($string->sub()->getVal() == $driver){
        $header = new \PPHP\tools\classes\standard\baseType\String('');
      }
      else{
        throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Отсутствуют данные для формирования объекта. Отсутствует заголовок запроса.');
      }
    }
    $header = Header::reestablish($header->getVal(), $driver);

    if($header->hasParameter('Content-Length')){
      $body = $string->subByte(null, (int)$header->getParameter('Content-Length')->getValue())->getVal();
      if($body === ''){
        throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Отсутствуют данные для формирования объекта. Отсутствует заявленное тело запроса.');
      }
    }
    else{
      $body = $string->sub()->getVal();
    }

    return new static($code, $message, $header, $body);
  }

  function __construct($code, $message, $header = null, $body = null){
    parent::__construct($header, $body);
    $this->code = $code;
    $this->message = $message;
  }

  /**
   * Метод возвращает строку, полученную при интерпретации объекта.
   * @param null|mixed $driver[optional] Данные, позволяющие изменить логику интерпретации объекта. Данный метод принимает символ конца строки для сериализации ответа.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты объекта.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver = null){
    $generalHeader = 'HTTP/1.1 '.$this->code.' '.$this->message;
    return $generalHeader . $driver . $this->header->interpretation($driver) . $driver . $this->body;
  }

  public function getCode(){
    return $this->code;
  }

  public function getMessage(){
    return $this->message;
  }
}
