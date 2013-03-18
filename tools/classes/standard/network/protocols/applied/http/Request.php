<?php
namespace PPHP\tools\classes\standard\network\protocols\applied\http;

/**
 * Класс представляет HTTP запрос клиента.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\network\protocols\applied\http
 */
class Request extends Message{
  /**
   * Метод GET запроса.
   */
  const GET = 'GET';
  /**
   * Метод POST запроса.
   */
  const POST = 'POST';

  /**
   * Метод запроса.
   * @var string
   */
  protected $method;
  /**
   * URI запроса.
   * @var string
   */
  protected $URI;

  /**
   * Метод восстанавливает объект из строки.
   * @param string $string Исходная строка.
   * @param null|mixed $driver[optional] Данные для восстановления. Данный метод принимает символ конца строки для парсинга запроса.
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
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Отсутствуют данные для формирования объекта. Отсутствует стартовая строка запроса.');
    }
    $method = $generalHeader->nextComponent(' ')->getVal();
    if($method === false){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Отсутствуют данные для формирования объекта. Отсутствует данные о методе запроса.');
    }
    $URI = $generalHeader->nextComponent(' ')->getVal();
    if($URI === false){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Отсутствуют данные для формирования объекта. Отсутствует данные о URI запроса.');
    }

    $header = $string->nextComponent($driver.$driver);
    if($header === false){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Отсутствуют данные для формирования объекта. Отсутствует заголовок запроса.');
    }
    $header = Header::reestablish($header->getVal(), $driver);
    if(!$header->hasParameter('Host')){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Отсутствуют данные для формирования объекта. Отсутствует адрес узла запроса.');
    }
    $host = $header->getParameter('Host')->getValue();

    if($header->hasParameter('Content-Length')){
      $body = $string->subByte(null, (int)$header->getParameter('Content-Length')->getValue())->getVal();
      if($body === ''){
        throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Отсутствуют данные для формирования объекта. Отсутствует заявленное тело запроса.');
      }
    }
    else{
      $body = $string->sub()->getVal();
    }

    return new static($host, $URI, $method, $header, $body);
  }

  /**
   * @param string $host Узел запроса и порт запроса.
   * @param string $URI Адрес ресурса.
   * @param string $method[optional] Метод запроса.
   * @param Header $header Заголовок запроса.
   * @param string|array $body[optional] Тело запроса в виде строки или ассоциативного массива параметров.
   */
  function __construct($host, $URI, $method = self::GET, Header $header = null, $body = null){
    $this->URI = $URI;
    if($method == self::POST){
      parent::__construct($header, $body);
    }
    elseif($method == self::GET){
      parent::__construct($header, null);
      if(is_array($body) && count($body) > 0){
        $parameters = [];
        foreach($body as $name => $value){
          $parameters[] = urlencode($name).'='. urlencode($value);
        }
        $this->URI .= '?'.implode('&', $parameters);
      }
    }
    $this->method = $method;
    $this->header->addParameterStr('Host', $host);
  }

  /**
   * Метод возвращает строку, полученную при интерпретации объекта.
   * @param null|mixed $driver[optional] Данные, позволяющие изменить логику интерпретации объекта. Данный метод принимает символ конца строки для сериализации запроса.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver = null){
    $generalHeader = $this->method . ' ' . $this->URI . ' HTTP/1.1';
    return $generalHeader . $driver . $this->header->interpretation($driver) . $driver . $this->body;

  }

  /**
   * @return string
   */
  public function getMethod(){
    return $this->method;
  }

  /**
   * @return mixed
   */
  public function getURI(){
    return $this->URI;
  }
}