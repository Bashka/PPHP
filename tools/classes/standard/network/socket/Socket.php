<?php
namespace PPHP\tools\classes\standard\network\socket;

/**
 * Объекты данного класса представляют собой сокеты, готовые к соединению (клиенские) или прослушивающие некоторый порт адреса (серверные).
 *
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
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException Выбрасывается в случае невозможности создания нового сокета.
   */
  private function createSocket(){
    $this->resource = socket_create($this->domain, $this->type, $this->protocol);
    if($this->resource === false){
      $code = socket_last_error();
      throw new \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException('Невозможно создать сокет. '. socket_strerror($code), $code);
    }
  }

  /**
   * @param integer $domain[optional] Домен соединения.
   * @param integer $type[optional] Тип сокета.
   * @param integer $protocol[optional] Протокол передачи данных.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException Выбрасывается в случае невозможности создания сокета.
   */
  public function __construct($domain = self::IPv4, $type = self::STREAM, $protocol = self::TCP){
    $this->domain = $domain;
    $this->type = $type;
    $this->protocol = $protocol;
    $this->createSocket();
  }

  /**
   * Метод использует сокет как клиенский и выполняет соединение с удаленным серверным сокетом, возвращая поток ввода/вывода.
   *
   * Вызов данного метода возвращает поток ввода/вывода к указанному удаленному сокету и освобождает данный сокет тем самым позволяя повторно использовать его для соединения.
   * @param string $address[optional] Адрес удаленного сокета в формате IPv4 или IPv6.
   * @param integer $port[optional] Порт, который прослушивает удаленный сокет.
   * @return \PPHP\tools\classes\standard\network\socket\Stream Поток ввода/вывода к указанному удаленному сокету.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException Выбрасывается в случае, если невозможно выполнить соединение с удаленным сокетом или обнулить текущий сокет.
   */
  public function connect($address='127.0.0.1', $port = 80){
    $result = socket_connect($this->resource, $address, $port);
    if($result === false){
      $code = socket_last_error($this->resource);
      throw new \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException('Невозможно выполнить соединение с удаленным сокетом. '. socket_strerror($code), $code);
    }
    $stream = new Stream(new InStream($this->resource), new OutStream($this->resource));
    $this->createSocket();
    return $stream;
  }

  /**
   * Метод регистрирует данный сокет как серверный и заставляет его прослушивать указанный порт на адресе сетевого узла.
   * @param string $address[optional] Прослушиваемый сетевой узел.
   * @param integer $port[optional] Прослушиваемый порт.
   * @param boolean $isBlock[optional] false - делает сокет не блокирующим.
   * @param integer $backlog[optional] Максимальный размер очереди ожидания сокета.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException Выбрасывается в случае, если невозможно изменить свойства сокета или привязать сокет к указанному адресу и порту.
   */
  public function listen($address='localhost', $port = 8080, $isBlock = true, $backlog = 0){
    if(!$isBlock){
      if(socket_set_nonblock($this->resource) === false){
        $code = socket_last_error($this->resource);
        throw new \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException('Невозможно изменить свойства сокета. '. socket_strerror($code), $code);
      }
    }
    if(socket_bind($this->resource, $address, $port) === false){
      $code = socket_last_error($this->resource);
      throw new \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException('Невозможно привязать сокет к адресу. '. socket_strerror($code), $code);
    }
    if(socket_listen($this->resource, $backlog) === false){
      $code = socket_last_error($this->resource);
      throw new \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException('Невозможно прослушать указанный адрес. '. socket_strerror($code), $code);
    }
  }

  /**
   * Метод прослушивает очередь подключения к данному сокету с целью получения ожидающего клиента.
   * @return boolean|\PPHP\tools\classes\standard\network\socket\Stream Возвращает поток к удаленному клиенскому сокету или false - если на момент вызова метода нет ожидающих клиенских сокетов.
   */
  public function accept(){
    if($steam = socket_accept($this->resource)){
      return new Stream(new InStream($steam), new OutStream($steam));
    }
    else{
      return false;
    }
  }

  /**
   * Метод отключает серверный сокет от прослушиваемого порта узла.
   * После выполнения этого метода сокет можно использовать повторно как серверный, а так же как клиенский.
   * @throws \PPHP\tools\patterns\io\IOException Выбрасывается в случае невозможности отключения сокета.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException Выбрасывается в случае невозможности обнуления сокета.
   */
  public function shutdown(){
    if($this->protocol != self::TCP && $this->protocol != self::UDP){
      if(socket_shutdown($this->resource) === false){
        $code = socket_last_error($this->resource);
        throw new \PPHP\tools\patterns\io\IOException('Ошибка закрытия сокета.'.socket_strerror($code), $code);
      }
    }
    if(socket_close($this->resource) === false){
      $code = socket_last_error($this->resource);
      throw new \PPHP\tools\patterns\io\IOException('Ошибка закрытия сокета.'.socket_strerror($code), $code);
    }
    $this->createSocket();
  }
}
