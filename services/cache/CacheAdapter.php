<?php
namespace PPHP\services\cache;

/**
 * Объекты-адаптеры, предоставляющие интерфейс для работы с кэш-системами.
 */
interface CacheAdapter{
  /**
   * Метод записывает значение в кэш.
   * @abstract
   * @param string $key Ключ значения.
   * @param mixed $value Значение.
   * @param null|integer $time Время кэширования в секундах.
   * @return boolean true - если запись успешна, иначе - false.
   */
  public function set($key, $value, $time=null);

  /**
   * Метод возвращает данные из кэша.
   * @abstract
   * @param string $key Ключ запрашиваемого значения.
   * @return string|boolean Ассоциированное с ключем значение или false, если значение не установленно.
   */
  public function get($key);

  /**
   * Метод устанавливает соединение с кэш-системой.
   * @abstract
   * @param string $host Адрес сервера, на котором располагается система.
   * @param null|integer $port Порт для соединения.
   * @return boolean true - если соединение успешно, иначе - false.
   */
  public function connect($host, $port=null);
}
