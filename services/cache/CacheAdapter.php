<?php
namespace PPHP\services\cache;

/**
 * Объекты-адаптеры, предоставляющие интерфейс для работы с кэш-системами.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\services\cache
 */
abstract class CacheAdapter{
  /**
   * Метод записывает значение в кэш.
   * @abstract
   * @param string $key Ключ значения.
   * @param mixed $value Значение.
   * @param null|integer $time [optional] Время кэширования в секундах.
   * @return boolean true - если запись успешна, иначе - false.
   */
  public abstract function set($key, $value, $time = null);

  /**
   * Метод возвращает данные из кэша.
   * @abstract
   * @param string $key Ключ запрашиваемого значения.
   * @return string|null Ассоциированное с ключем значение или null, если значение не установленно.
   */
  public abstract function get($key);

  /**
   * Метод удаляет данные из кэша.
   * @abstract
   * @param string $key Ключ удаляемого значения.
   * @return boolean true - если удаление выполнено, иначе - false.
   */
  public abstract function remove($key);

  /**
   * Метод устанавливает соединение с кэш-системой.
   * @abstract
   * @param string $host Адрес сервера, на котором располагается система.
   * @param null|integer $port Порт для соединения.
   * @return boolean true - если соединение успешно, иначе - false.
   */
  public abstract function connect($host, $port = null);

  function __set($name, $value){
    $this->set($name, $value);
  }

  function __get($name){
    return $this->get($name);
  }

  function __isset($name){
    return $this->get($name) !== null;
  }

  function __unset($name){
    $this->remove($name);
  }
}
