<?php
namespace PPHP\tools\classes\standard\network\protocols\applied\http;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use \PPHP\tools\classes\standard\baseType as baseType;

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
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    if(is_null($driver)){
      $driver = "\r\n";
    }
    return ['(' . self::GET . '|' . self::POST . ') ((?:' . baseType\special\fileSystem\FileSystemAddress::getMasks()[0] . '|\/)(?:\?'.self::getPatterns()['var'].'(?:&'.self::getPatterns()['var'].')*)?) HTTP\/1.1' . $driver . '(' . Header::getMasks($driver)[0] . ')?' . $driver . '(.*)'];
  }

  /**
   * Метод должен возвращать массив шаблонов, описывающих различные компоненты шаблонов верификации.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getPatterns($driver = null){
    return ['var' => '[A-Za-z0-9_]+=(?:[A-Za-z0-9_]+)?'];
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

    $method = $m[1];
    $uri = $m[2];
    $header = Header::reestablish($m[4]);
    $host = $header->getParameterValue('Host');
    $body = $m[7];
    return new self($host, $uri, $method, $header, $body);
  }

  /**
   * Метод восстанавливает объект из строки.
   * @abstract
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Разделитель заголовка и тела запроса. По умолчанию PHP_EOL.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты строки.
   * @throws exceptions\StructureException Выбрасывается в случае, если исходная строка не отвечает требования структуры.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return mixed Результирующий объект.
   */
  /*public static function reestablish($string, $driver = null){
    if($string == ''){
      throw new exceptions\NotFoundDataException('Отсутствуют данные для формирования объекта.');
    }
    if(is_null($driver)){
      $driver = PHP_EOL;
    }
    $string = new baseType\String($string);

    $generalHeader = $string->nextComponent($driver);
    if($generalHeader === false){
      throw new exceptions\NotFoundDataException('Отсутствуют данные для формирования объекта. Отсутствует стартовая строка запроса.');
    }
    $method = $generalHeader->nextComponent(' ')->getVal();
    if($method === false){
      throw new exceptions\NotFoundDataException('Отсутствуют данные для формирования объекта. Отсутствует данные о методе запроса.');
    }
    $URI = $generalHeader->nextComponent(' ')->getVal();
    if($URI === false){
      throw new exceptions\NotFoundDataException('Отсутствуют данные для формирования объекта. Отсутствует данные о URI запроса.');
    }

    $header = $string->nextComponent($driver.$driver);
    if($header === false){
      throw new exceptions\NotFoundDataException('Отсутствуют данные для формирования объекта. Отсутствует заголовок запроса.');
    }
    $header = Header::reestablish($header->getVal(), $driver);
    if(!$header->hasParameter('Host')){
      throw new exceptions\NotFoundDataException('Отсутствуют данные для формирования объекта. Отсутствует адрес узла запроса.');
    }
    $host = $header->getParameter('Host')->getValue();

    if($header->hasParameter('Content-Length')){
      $body = $string->subByte(null, (int)$header->getParameter('Content-Length')->getValue())->getVal();
      if($body === ''){
        throw new exceptions\NotFoundDataException('Отсутствуют данные для формирования объекта. Отсутствует заявленное тело запроса.');
      }
    }
    else{
      $body = $string->sub()->getVal();
    }

    return new static($host, $URI, $method, $header, $body);
  }*/

  /**
   * @param string $host Узел запроса и порт запроса.
   * @param string $URI Адрес ресурса.
   * @param string $method [optional] Метод запроса.
   * @param Header $header [optional] Заголовок запроса.
   * @param string|array $body [optional] Тело запроса в виде строки или ассоциативного массива параметров.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   */
  function __construct($host, $URI, $method = self::GET, Header $header = null, $body = null){

    exceptions\InvalidArgumentException::verifyType($host, 'S');
    exceptions\InvalidArgumentException::verifyType($URI, 'S');
    exceptions\InvalidArgumentException::verifyType($method, 'S');
    $this->URI = $URI;
    if($method == self::POST){
      parent::__construct($header, $body);
    }
    elseif($method == self::GET){
      parent::__construct($header, null);
      if(is_array($body) && count($body) > 0){
        $parameters = [];
        foreach($body as $name => $value){
          $parameters[] = urlencode($name) . '=' . urlencode($value);
        }
        $this->URI .= '?' . implode('&', $parameters);
      }
      else{
        parent::__construct($header, $body);
      }
    }
    $this->method = $method;
    $this->header->addParameterStr('Host', $host);
  }

  /**
   * Метод возвращает строку, полученную при интерпретации объекта.
   * @abstract
   * @param mixed $driver [optional] Разделитель компонентов запроса. По умолчанию PHP_EOL.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver = null){
    if(is_null($driver)){
      $driver = "\r\n";
    }
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