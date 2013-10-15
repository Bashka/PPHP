<?php
namespace PPHP\tools\classes\standard\network\protocols\applied\http;

use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use \PPHP\tools\patterns\interpreter as interpreter;

/**
 * Класс представляет HTTP передаваемое или получаемое сообщение.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\network\protocols\applied\http
 */
abstract class Message extends interpreter\RestorableAdapter implements interpreter\Interpreter{
  /**
   * @var \PPHP\tools\classes\standard\network\protocols\applied\http\Header Заголовок сообщения.
   */
  protected $header;

  /**
   * @var string Тело сообщения.
   */
  protected $body;

  /**
   * @param \PPHP\tools\classes\standard\network\protocols\applied\http\Header $header [optional] Заголовок сообщения.
   * @param string|array $body [optional] Тело сообщения в виде строки или ассоциативного массива параметров, передаваемых в сообщении. В случае передачи массива тело формируется следующим образом: <ключ элемента>:<значение элемента>&
   */
  function __construct($header = null, $body = null){
    $this->header = ($header !== null)? $header : new Header();
    $this->header->addParameterStr('Cache-Control', 'no-cache');
    $this->header->addParameterStr('Connection', 'close');
    if($body !== null){
      if(is_array($body)){
        if(count($body) > 0){
          $strBody = [];
          foreach($body as $name => $value){
            $strBody[] = urlencode($name) . '=' . urlencode($value);
          }
          $body = implode('&', $strBody);
        }
        else{
          $body = '';
        }
      }
      $this->setBody($body);
    }
  }

  /**
   * Метод устанавливает указанный заголовок для данного сообщения.
   * @param \PPHP\tools\classes\standard\network\protocols\applied\http\Header $header Устанавливаемый заголовок.
   */
  public function setHeader(Header $header){
    $this->header = $header;
  }

  /**
   * Метод добавляет параметр к заголовку сообщения.
   * @param \PPHP\tools\classes\standard\network\protocols\applied\http\Parameter $parameter Добавляемый параметр.
   */
  public function addParameterHeader(Parameter $parameter){
    $this->header->addParameter($parameter);
  }

  /**
   * Метод создает параметр из строки и добавляет его к заговолку сообщения.
   * @param string $name Имя параметра.
   * @param string $value Значение параметра.
   */
  public function addParameterHeaderStr($name, $value){
    $this->header->addParameterStr($name, $value);
  }

  /**
   * Метод устанвливает тело сообщения.
   * Данный метод позволяет так же задать тип и кодировку передаваемых данных в том случае, если они не были заданы заранее.
   * Выполнение метода сопровождается установкой параметров заголовка Content-Length и Content-MD5.
   * @param string|integer|float $body Тело сообщения.
   * @param string $type [optional] Тип данных тела.
   * @param string $charset [optional] Кодировка данных тела.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения пустого тела запроса.
   */
  public function setBody($body, $type = 'application/x-www-form-urlencoded', $charset = 'utf-8'){
    InvalidArgumentException::verifyType($body, 'sif');
    $body = (string) $body;
    $this->body = $body;
    if(!$this->header->hasParameter('Content-Type')){
      $this->header->addParameterStr('Content-Type', $type . ';charset=' . $charset);
    }
    $this->header->addParameterStr('Content-Length', strlen($body));
    $this->header->addParameterStr('Content-MD5', md5($body));
  }

  /**
   * Возвращает заголовок сообщения.
   * @return \PPHP\tools\classes\standard\network\protocols\applied\http\Header Заголовок сообщения.
   */
  public function getHeader(){
    return $this->header;
  }

  /**
   * Возвращает тело сообщения.
   * @return null|string Тело сообщения или null - если тело не установлено.
   */
  public function getBody(){
    return $this->body;
  }
}
