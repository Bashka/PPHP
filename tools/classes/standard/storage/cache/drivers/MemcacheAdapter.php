<?php
namespace PPHP\tools\classes\standard\storage\cache\drivers;

use PPHP\tools\classes\standard\storage\cache\CacheAdapter;

/**
 * Адаптер, для взаимодействия с memcache.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\storage\cache\drivers
 */
class MemcacheAdapter extends CacheAdapter{
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
   * @return string|null Ассоциированное с ключем значение или null, если значение не установленно.
   */
  public function get($key){
    $result = $this->cache->get($key);
    if($result === false){
      return null;
    }

    return $result;
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

  /**
   * Метод удаляет данные из кэша.
   * @param string $key Ключ удаляемого значения.
   * @return boolean true - если удаление выполнено, иначе - false.
   */
  public function remove($key){
    return $this->cache->delete($key);
  }
}
