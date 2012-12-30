<?php
namespace PPHP\services\database;

/**
 * Класс позволяет соединиться с базой данных.
 */
class ConnectionManager implements \PPHP\tools\patterns\singleton\Singleton{
use \PPHP\tools\patterns\singleton\TSingleton;

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
   * @var \PPHP\tools\classes\standard\storage\database\PDO
   */
  protected $PDO;

  /**
   * @var \PPHP\services\configuration\Configurator
   */
  protected $conf;
  /**
   * @var \PPHP\services\cache\CacheAdapter
   */
  protected $cache;

  /**
   * Конструктор использует конфигурацию системы для инициализации интерфейса доступа к СУБД.
   *
   * Метод использует систему кэширования, что позволяет реже обращаться к файловой системе для хранения данных инициализации.
   * В случае, если кэширование отключено, метод работает с файловой системой при каждом обращении к нему.
   * @throws \PPHP\services\InitializingDataNotFoundException Выбрасывается в случае, если не удалось инициализировать соединение.
   */
  private function __construct(){
    $this->cache = \PPHP\services\cache\CacheSystem::getInstance();

    if(!isset($this->cache->ConnectionManager_Driver)){
      $this->conf = \PPHP\services\configuration\Configurator::getInstance();
      if(!isset($this->conf->Database_Driver) || !isset($this->conf->Database_Host) || !isset($this->conf->Database_DBName) || !isset($this->conf->Database_User)){
        throw new \PPHP\services\InitializingDataNotFoundException('Недостаточно данных для инициализации, необходимыми полями являются: Driver, Host, DBName, User');
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
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\PDOException Выбрасывается в случае возникновения ошибки при подключении к БД.
   * @return \PPHP\tools\classes\standard\storage\database\PDO Соединение с БД.
   */
  public function getNewPDO(){
    return new \PPHP\tools\classes\standard\storage\database\PDO($this->createDSN(), $this->user, $this->password);
  }

  /**
   * Метод возвращает постоянное соединение с БД.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\PDOException Выбрасывается в случае возникновения ошибки при подключении к БД.
   * @return \PPHP\tools\classes\standard\storage\database\PDO Соединение с БД.
   */
  public function getPDO(){
    if(empty($this->PDO)){
      $this->PDO = new \PPHP\tools\classes\standard\storage\database\PDO($this->createDSN(), $this->user, $this->password, [\PDO::ATTR_PERSISTENT => true]);
    }
    return $this->PDO;
  }

  /**
   * Метод изменяет одно из инициализирующих свойств на данное.
   * @param string $attributeName Имя изменяемого свойства.
   * @param string $value Новое значение свойства.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве свойства задан недопустимый аргумент или значения переданных аргументов имеют неверный тип.
   */
  public function setAttribute($attributeName, $value){
    if(!is_string($attributeName) || !is_string($value)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException();
    }
    if(array_search($attributeName, ['Driver', 'Host', 'DBName', 'Article', 'Password']) == -1){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException();
    }
    if($attributeName != 'Password' && empty($value)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException();
    }
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
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если значение переданного аргумента имеет неверный тип.
   * @return null|string
   */
  public function getAttribute($attributeName){
    if(!is_string($attributeName)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $attributeName);
    }

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