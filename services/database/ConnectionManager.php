<?php
namespace PPHP\services\database;
use PPHP\services\cache\CacheAdapter;
use PPHP\services\cache\CacheSystem;
use PPHP\services\configuration\Configurator;
use PPHP\tools\classes\standard\fileSystem\NotExistsException;
use PPHP\tools\classes\standard\storage\database\PDO;
use \PPHP\tools\patterns\singleton as singleton;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс позволяет соединиться с базой данных.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\services\database
 */
class ConnectionManager implements singleton\Singleton{
  use singleton\TSingleton;

  /**
   * Драйвер соединения с БД.
   * @var string
   */
  protected $driver;

  /**
   * Адрес сервера БД.
   * @var string
   */
  protected $host;

  /**
   * Имя БД для соединения.
   * @var string
   */
  protected $dbName;

  /**
   * Логин соединения.
   * @var string
   */
  protected $user;

  /**
   * Пароль соединения.
   * @var string|null
   */
  protected $password;

  /**
   * Активное соединение с БД.
   * @var PDO
   */
  protected $PDO;

  /**
   * @var Configurator
   */
  protected $conf;

  /**
   * @var CacheAdapter
   */
  protected $cache;

  /**
   * Конструктор использует конфигурацию системы для инициализации интерфейса доступа к СУБД.
   * Метод использует систему кэширования, что позволяет реже обращаться к файловой системе для хранения данных инициализации.
   * В случае, если кэширование отключено, метод работает с файловой системой при каждом обращении к нему.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если не удалось инициализировать соединение или смежные подсистемы.
   */
  private function __construct(){
    try{
      $this->cache = CacheSystem::getInstance();
    }
    catch(exceptions\NotFoundDataException $e){
      throw $e;
    }


    if(!CacheSystem::hasCache() || !isset($this->cache->ConnectionManager_Driver)){
      try{
        $this->conf = Configurator::getInstance();
      }
      catch(NotExistsException $e){
        throw new exceptions\NotFoundDataException('Не удалось получить доступ к конфигурации системы.', 1, $e);
      }

      if(!isset($this->conf->Database_Driver) || !isset($this->conf->Database_Host) || !isset($this->conf->Database_DBName) || !isset($this->conf->Database_User)){
        throw new exceptions\NotFoundDataException('Недостаточно данных для инициализации, необходимыми полями являются: Driver, Host, DBName, User');
      }
      $this->driver = $this->conf->Database_Driver;
      $this->host = $this->conf->Database_Host;
      $this->dbName = $this->conf->Database_DBName;
      $this->user = $this->conf->Database_User;
      $this->password = (isset($this->conf->Database_Password))? $this->conf->Database_Password : '';

      $this->cache->ConnectionManager_Driver = $this->driver;
      $this->cache->ConnectionManager_Host = $this->host;
      $this->cache->ConnectionManager_DBName = $this->dbName;
      $this->cache->ConnectionManager_User = $this->user;
      $this->cache->ConnectionManager_Password = $this->password;
    }
    else{
      $this->driver = $this->cache->ConnectionManager_Driver;
      $this->host = $this->cache->ConnectionManager_Host;
      $this->dbName = $this->cache->ConnectionManager_DBName;
      $this->user = $this->cache->ConnectionManager_User;
      $this->password = $this->cache->ConnectionManager_Password;
    }
  }

  protected function createDSN(){
    return $this->driver . ':host=' . $this->host . ';dbname=' . $this->dbName . ';charset=UTF8';
  }

  /**
   * Метод возвращает новое соединение с БД.
   * @throws exceptions\PDOException Выбрасывается в случае возникновения ошибки при подключении к БД.
   * @return PDO Соединение с БД.
   */
  public function getNewPDO(){
    try{
      return new PDO($this->createDSN(), $this->user, $this->password);
    }
    catch(exceptions\PDOException $e){
      throw $e;
    }
  }

  /**
   * Метод возвращает постоянное соединение с БД.
   * @throws exceptions\PDOException Выбрасывается в случае возникновения ошибки при подключении к БД.
   * @return PDO Соединение с БД.
   */
  public function getPDO(){
    if(empty($this->PDO)){
      try{
        $this->PDO = new PDO($this->createDSN(), $this->user, $this->password, [\PDO::ATTR_PERSISTENT => true]);
      }
      catch(exceptions\PDOException $e){
        throw $e;
      }
    }
    return $this->PDO;
  }

  /**
   * Метод изменяет одно из инициализирующих свойств на данное.
   * @param string $attributeName Имя изменяемого свойства.
   * @param string $value Новое значение свойства.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае, если недопустимый параметр или значения переданных параметров имеют неверный тип.
   */
  public function setAttribute($attributeName, $value){
    exceptions\InvalidArgumentException::verifyType($attributeName, 'S');
    exceptions\InvalidArgumentException::verifyType($value, 'S');
    exceptions\InvalidArgumentException::verifyVal($attributeName, 's # Driver|Host|DBName|User|Password');

    $this->conf->set('Database', $attributeName, $value);
    switch($attributeName){
      case 'Driver':
        $this->driver = $value;
        $this->cache->Database_Driver = $value;
        break;
      case 'Host':
        $this->host = $value;
        $this->cache->Database_Host = $value;
        break;
      case 'DBName':
        $this->dbName = $value;
        $this->cache->Database_DBName = $value;
        break;
      case 'User':
        $this->user = $value;
        $this->cache->Database_User = $value;
        break;
      case 'Password':
        $this->password = $value;
        $this->cache->Database_Password = $value;
        break;
    }
  }

  /**
   * Метод возвращает значение заданного инициализирующего свойства.
   * @param string $attributeName Имя инициализирующего свойства.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае, если значение переданного аргумента имеет неверный тип.
   * @return string
   */
  public function getAttribute($attributeName){
    exceptions\InvalidArgumentException::verifyType($attributeName, 'S');

    switch($attributeName){
      case 'Driver':
        return $this->driver;
        break;
      case 'Host':
        return $this->host;
        break;
      case 'DBName':
        return $this->dbName;
        break;
      case 'User':
        return $this->user;
        break;
      case 'Password':
        return $this->password;
        break;
      default:
        return null;
    }
  }
}