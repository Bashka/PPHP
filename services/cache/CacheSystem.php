<?php
namespace PPHP\services\cache;

/**
 * Класс представляет глобальную фабрику адаптеров, для работы с используемыми кэш-системами.
 */
class CacheSystem implements \PPHP\tools\patterns\singleton\Singleton{
  /**
   * Текущий адаптер кэш-системы.
   * @var CacheAdapter
   */
  protected static $adapter;

  /**
   * Метод возвращает экземпляр класса драйвера.
   * @static
   * @throws \PPHP\services\InitializingDataNotFoundException Выбрасывается в случае, если не удалось инициализировать кэш-систему.
   * @return CacheAdapter
   */
  public static function getInstance(){
    if(empty(self::$adapter)){
      $conf = \PPHP\services\configuration\Configurator::getInstance();
      if(!isset($conf->Cache_Driver) || !isset($conf->Cache_Server)){
        throw new \PPHP\services\InitializingDataNotFoundException('Недостаточно данных для инициализации, необходимыми полями являются: Driver, Server');
      }
      $adapterName = '\PPHP\services\cache\drivers\\' . $conf->Cache_Driver;
      $adapter = new $adapterName;
      $serverOption = $conf->Cache_Server;
      $serverOption = explode(':', $serverOption);
      $adapter->connect($serverOption[0], $serverOption[1]);
      self::$adapter = $adapter;
    }
    return self::$adapter;
  }

  /**
   * Метод определяет, используется ли утилита кэширования в системе.
   * @static
   * @return boolean true - если кэширование используется, иначе - false.
   */
  public static function hasCache(){
    $conf = \PPHP\services\configuration\Configurator::getInstance();
    return $conf->Cache_Driver != 'NullAdapter';
  }

  /**
   * Метод конфигурирует службу.
   * @static
   * @param string $attributeName Свойство конфигурации. Доступными свойствами являются: Driver - имя адапрета; Server - кэш-сервер.
   * @param string $value Значение конфигурации.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException
   */
  public static function setAttribute($attributeName, $value){
    if(!is_string($attributeName) || !is_string($value)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $attributeName);
    }
    if(!array_search($attributeName, ['Driver', 'Server'])){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
    }
    $conf = \PPHP\services\configuration\Configurator::getInstance();
    $conf->set('Cache', $attributeName, $value);
  }
}
