<?php
namespace PPHP\tools\classes\standard\storage\cache;

use PPHP\services\configuration\Configurator;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\singleton\Singleton;
use PPHP\tools\classes\standard\fileSystem\NotExistsException;

/**
 * Класс представляет глобальную фабрику адаптеров, для работы с используемыми кэш-системами.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\storage\cache
 */
class Cache implements Singleton{
  /**
   * Маркерная аннотация, определяющая классы, объекты которых должны кэшироваться.
   */
  const CACHE_CACHE = 'Cache\Cache';

  /**
   * Текущий адаптер кэш-системы.
   * @var CacheAdapter
   */
  protected static $adapter;

  /**
   * Метод возвращает экземпляр класса драйвера.
   * @static
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если не удалось инициализировать кэш-систему.
   * @return CacheAdapter
   */
  public static function getInstance(){
    if(empty(self::$adapter)){
      try{
        $conf = Configurator::getInstance();
      }
      catch(NotExistsException $e){
        throw new exceptions\NotFoundDataException('Не удалось получить доступ к конфигурации системы.', 1, $e);
      }
      if(!isset($conf->Cache_Driver) || !isset($conf->Cache_Server)){
        throw new exceptions\NotFoundDataException('Недостаточно данных для инициализации, необходимыми полями являются: Driver, Server');
      }
      $adapterName = '\PPHP\tools\classes\standard\storage\cache\drivers\\' . $conf->Cache_Driver;
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
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если не удалось инициализировать кэш-систему.
   * @return boolean true - если кэширование используется, иначе - false.
   */
  public static function hasCache(){
    try{
      $conf = Configurator::getInstance();
    }
    catch(NotExistsException $e){
      throw new exceptions\NotFoundDataException('Не удалось получить доступ к конфигурации системы.', 1, $e);
    }

    return $conf->Cache_Driver != 'NullAdapter';
  }

  /**
   * Метод конфигурирует службу.
   * @static
   * @param string $attributeName Свойство конфигурации. Доступными свойствами являются: Driver - имя адапрета; Server - кэш-сервер.
   * @param string $value Значение конфигурации.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае, если недопустимый параметр или значения переданных параметров имеют неверный тип.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если не удалось инициализировать кэш-систему.
   */
  public static function setAttribute($attributeName, $value){
    exceptions\InvalidArgumentException::verifyType($attributeName, 'S');
    exceptions\InvalidArgumentException::verifyType($value, 'S');
    exceptions\InvalidArgumentException::verifyVal($attributeName, 's # Driver|Server');
    try{
      $conf = Configurator::getInstance();
    }
    catch(NotExistsException $e){
      throw new exceptions\NotFoundDataException('Не удалось получить доступ к конфигурации системы.', 1, $e);
    }
    $conf->set('Cache', $attributeName, $value);
  }
}
