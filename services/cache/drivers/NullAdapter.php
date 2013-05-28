<?php
namespace PPHP\services\cache\drivers;
use PPHP\services\cache\CacheAdapter;


/**
 * Объект-пустышка, применяемый в случае отсутствия кэш-системы.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\services\cache\drivers
 */
class NullAdapter extends CacheAdapter{
  /**
   * Метод записывает значение в кэш.
   * @param string $key Ключ значения.
   * @param mixed $value Значение.
   * @param null|integer $time Время кэширования в секундах.
   * @return boolean true - если запись успешна, иначе - false.
   */
  public function set($key, $value, $time = null){
    return true;
  }

  /**
   * Метод возвращает данные из кэша.
   * @param string $key Ключ запрашиваемого значения.
   * @return string|null Ассоциированное с ключем значение или null, если значение не установленно.
   */
  public function get($key){
    return null;
  }

  /**
   * Метод устанавливает соединение с кэш-системой.
   * @param string $host Адрес сервера, на котором располагается система.
   * @param null|integer $port Порт для соединения.
   * @return boolean true - если соединение успешно, иначе - false.
   */
  public function connect($host, $port = null){
    return true;
  }

  /**
   * Метод удаляет данные из кэша.
   * @param string $key Ключ удаляемого значения.
   * @return boolean true - если удаление выполнено, иначе - false.
   */
  public function remove($key){
    return true;
  }

}
