<?php
namespace PPHP\services\cache\drivers;


/**
 * Объект-пустышка, применяемый в случае отсутствия кэш-системы.
 */
class NullAdapter extends \PPHP\services\cache\CacheAdapter{
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
   * @return string|boolean Ассоциированное с ключем значение или false, если значение не установленно.
   */
  public function get($key){
    return false;
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
