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
   * Метод возвращает экземпляр данного класса
   * @static
   * @throws \PPHP\services\InitializingDataNotFoundException Выбрасывается в случае, если не удалось инициализировать кэш-систему.
   * @return CacheAdapter
   */
  static public function getInstance(){
    if(empty(self::$adapter)){
      $conf = \PPHP\services\configuration\Configurator::getInstance();
      if(!$conf->isExists('Cache', 'Driver') || !$conf->isExists('Cache', 'Server')){
        throw new \PPHP\services\InitializingDataNotFoundException('Недостаточно данных для инициализации, необходимыми полями являются: Driver, Server');
      }
      $adapterName = '\PPHP\services\cache\drivers\\' . $conf->get('Cache', 'Driver');
      $adapter = new $adapterName;
      $serverOption = $conf->get('Cache', 'Server');
      $serverOption = explode(':', $serverOption);
      $adapter->connect($serverOption[0], $serverOption[1]);
      self::$adapter = $adapter;
    }
    return self::$adapter;
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
