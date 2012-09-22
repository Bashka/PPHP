<?php
namespace PPHP\services\cache\drivers;


/**
 * Адаптер, для взаимодействия с memcache.
 */
class MemcacheAdapter implements \PPHP\services\cache\CacheAdapter{
  /**
   * Не адаптированный объект взаимодействия с кэш-системой.
   * @var \Memcache
   */
  private $cache;

  function __construct(){
    $this->cache = new \Memcache();
  }

  /**
   * Метод записывает значение в кэш.
   * @param string $key Ключ значения.
   * @param mixed $value Значение.
   * @param null|integer $time Время кэширования в секундах.
   * @return boolean true - если запись успешна, иначе - false.
   */
  public function set($key, $value, $time = null){
    return $this->cache->set($key, $value, $time);
  }

  /**
   * Метод возвращает данные из кэша.
   * @param string $key Ключ запрашиваемого значения.
   * @return string|boolean Ассоциированное с ключем значение или false, если значение не установленно.
   */
  public function get($key){
    return $this->cache->get($key);
  }

  /**
   * Метод устанавливает соединение с кэш-системой.
   * @param string $host Адрес сервера, на котором располагается система.
   * @param null|integer $port Порт для соединения.
   * @return boolean true - если соединение успешно, иначе - false.
   */
  public function connect($host, $port = null){
    return $this->cache->connect($host, $port);
  }

}
