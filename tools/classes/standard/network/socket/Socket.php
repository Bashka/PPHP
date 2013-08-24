<?php
namespace PPHP\tools\classes\standard\network\socket;

use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use \PPHP\tools\patterns\io as io;

/**
 * Объекты данного класса представляют собой сокеты, готовые к соединению (клиенские) или прослушивающие некоторый порт адреса (серверные).
 * Инстанциация данного класса создает готовый к работе сокет, который можно использовать как клиенский или серверный вариант, достаточно лишь вызвать соответствующий метод.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\network\socket
 */
class Socket{
  /**
   * Домен сокетного соединения. Протокол Internet IPv4.
   */
  const IPv4 = AF_INET;

  /**
   * Домен сокетного соединения. Протокол Internet IPv6.
   */
  const IPv6 = AF_INET6;

  /**
   * Домен сокетного соединения. Локальное межпроцессное взаимодействие.
   */
  const LOC = AF_UNIX;

  /**
   * Тип сокета. Последовательный, надежный, двунаправленный поток основанный на подключении.
   */
  const STREAM = SOCK_STREAM;

  /**
   * Тип сокета. Ненадежный сокет, без подключения, передающий данные фиксированной длины (дайтаграммы).
   */
  const DGRAM = SOCK_DGRAM;

  /**
   * Протокол передачи данных TCP.
   */
  const TCP = SOL_TCP;

  /**
   * Протокол передачи данных UDP.
   */
  const UDP = SOL_UDP;

  /**
   * Тип домена сокета.
   * @var integer
   */
  private $domain;

  /**
   * Тип сокета.
   * @var integer
   */
  private $type;

  /**
   * Протокол передачи данных сокета.
   * @var integer
   */
  private $protocol;

  /**
   * Ресурс сокета.
   * @var \resource
   */
  private $resource;

  /**
   * Метод создает новый сокет для данного объекта.
   * @throws exceptions\RuntimeException Выбрасывается в случае невозможности создания нового сокета.
   */
  private function createSocket(){
    $this->resource = socket_create($this->domain, $this->type, $this->protocol);
    if($this->resource === false){
      $code = socket_last_error();
      throw new exceptions\RuntimeException('Невозможно создать сокет. ' . socket_strerror($code), $code);
    }
  }

  /**
   * @param integer $domain [optional] Домен соединения.
   * @param integer $type [optional] Тип сокета.
   * @param integer $protocol [optional] Протокол передачи данных.
   * @throws exceptions\RuntimeException Выбрасывается в случае невозможности создания сокета.
   */
  public function __construct($domain = self::IPv4, $type = self::STREAM, $protocol = self::TCP){
    $this->domain = $domain;
    $this->type = $type;
    $this->protocol = $protocol;
    try{
      $this->createSocket();
    }
    catch(exceptions\RuntimeException $exc){
      throw $exc;
    }
  }

  /**
   * Метод использует сокет как клиенский и выполняет соединение с удаленным серверным сокетом, возвращая поток ввода/вывода.
   * Вызов данного метода возвращает поток ввода/вывода к указанному удаленному сокету и освобождает данный сокет тем самым позволяя повторно использовать его для соединения.
   * @param string $address [optional] Адрес удаленного сокета в формате IPv4 или IPv6.
   * @param integer $port [optional] Порт, который прослушивает удаленный сокет.
   * @throws exceptions\RuntimeException Выбрасывается в случае, если невозможно выполнить соединение с удаленным сокетом или обнулить текущий сокет.
   * @return Stream Поток ввода/вывода к указанному удаленному сокету.
   */
  public function connect($address = '127.0.0.1', $port = 80){
    $result = socket_connect($this->resource, $address, $port);
    if($result === false){
      $code = socket_last_error($this->resource);
      throw new exceptions\RuntimeException('Невозможно выполнить соединение с удаленным сокетом. ' . socket_strerror($code), $code);
    }
    $stream = new Stream(new InStream($this->resource), new OutStream($this->resource));
    // Обновление сокета с целью повторного соединения
    try{
      $this->createSocket();
    }
    catch(exceptions\RuntimeException $e){
      throw $e;
    }

    return $stream;
  }

  /**
   * Метод регистрирует данный сокет как серверный и заставляет его прослушивать указанный порт на адресе сетевого узла.
   * @param string $address [optional] Прослушиваемый сетевой узел.
   * @param integer $port [optional] Прослушиваемый порт.
   * @param boolean $isBlock [optional] false - делает сокет не блокирующим.
   * @param integer $backlog [optional] Максимальный размер очереди ожидания сокета.
   * @throws exceptions\RuntimeException Выбрасывается в случае, если невозможно изменить свойства сокета или привязать сокет к указанному адресу и порту.
   */
  public function listen($address = 'localhost', $port = 8080, $isBlock = true, $backlog = 0){
    if(!$isBlock){
      if(socket_set_nonblock($this->resource) === false){
        $code = socket_last_error($this->resource);
        throw new exceptions\RuntimeException('Невозможно изменить свойства сокета. ' . socket_strerror($code), $code);
      }
    }
    socket_set_option($this->resource, SOL_SOCKET, SO_RCVTIMEO, ["sec" => 1, "usec" => 0]);
    if(socket_bind($this->resource, $address, $port) === false){
      $code = socket_last_error($this->resource);
      throw new exceptions\RuntimeException('Невозможно привязать сокет к адресу. ' . socket_strerror($code), $code);
    }
    if(socket_listen($this->resource, $backlog) === false){
      $code = socket_last_error($this->resource);
      throw new exceptions\RuntimeException('Невозможно прослушать указанный адрес. ' . socket_strerror($code), $code);
    }
  }

  /**
   * Метод прослушивает очередь подключения к данному сокету с целью получения ожидающего клиента.
   * @return boolean|Stream Возвращает поток к удаленному клиенскому сокету или false - если на момент вызова метода нет ожидающих клиенских сокетов.
   */
  public function accept(){
    if($stream = socket_accept($this->resource)){
      return new Stream(new InStream($stream), new OutStream($stream));
    }
    else{
      return false;
    }
  }

  /**
   * Метод отключает серверный сокет от прослушиваемого порта узла.
   * После выполнения этого метода сокет можно использовать повторно как серверный, а так же как клиенский.
   * Серверный сокет должен быть закрыт только после закрытия всех клиентских соединений, иначе это приведет к ошибке.
   * В случае, если соединение с клиенстким сокетом было выполнено, серверный сокет не закрывается сразу, а ждет несколько секунд дополнительных данных, которые могли отстать от основного потока.
   * @throws io\IOException Выбрасывается в случае невозможности отключения сокета.
   * @throws exceptions\RuntimeException Выбрасывается в случае невозможности обнуления сокета.
   */
  public function shutdown(){
    if($this->protocol != self::TCP && $this->protocol != self::UDP){
      if(socket_shutdown($this->resource) === false){
        $code = socket_last_error($this->resource);
        throw new io\IOException('Ошибка закрытия сокета.' . socket_strerror($code), $code);
      }
    }
    if(socket_close($this->resource) === false){
      $code = socket_last_error($this->resource);
      throw new io\IOException('Ошибка закрытия сокета.' . socket_strerror($code), $code);
    }
    try{
      $this->createSocket();
    }
    catch(exceptions\RuntimeException $e){
      throw $e;
    }
  }
}
