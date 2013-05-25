<?php
namespace PPHP\services\database\identification;

/**
 * Класс позволяет поддерживать неповторимость идентификатора по отношению к любому объекту в системе.
 */
class Autoincrement implements \PPHP\tools\patterns\singleton\Singleton{
use \PPHP\tools\patterns\singleton\TSingleton;

  /**
   * @var \PPHP\services\configuration\Configurator
   */
  protected $conf;

  /**
   * @var \PPHP\services\cache\CacheAdapter
   */
  protected $cache;

  /**
   * @throws \PPHP\services\InitializingDataNotFoundException Выбрасывается в случае, если не удалось инициализировать соединение.
   */
  private function __construct(){
    $this->conf = \PPHP\services\configuration\Configurator::getInstance();
    if(\PPHP\services\cache\CacheSystem::hasCache()){
      $this->cache = \PPHP\services\cache\CacheSystem::getInstance();
    }
  }

  /**
   * Метод возвращает текущее значение счетчика идентификатора.
   *
   * Метод использует систему кэширования, что позволяет реже обращаться к файловой системе для хранения счетчика идентификатора.
   * В случае, если кэширование отключено, метод работает с файловой системой при каждом обращении к нему.
   * @return string|null Текущее значение счетчика идентификатора или null - если файл конфигурации поврежден.
   */
  protected function get(){
    $OID = null;
    if($this->cache){
      if(isset($this->cache->Autoincrement_OID)){
        $OID = $this->cache->Autoincrement_OID;
      }
      else{
        $OID = $this->conf->Autoincrement_OID;
        if($OID !== null){
          $this->cache->Autoincrement_OID = $OID;
        }
      }
    }
    else{
      $OID = $this->conf->Autoincrement_OID;
    }
    return $OID;
  }

  /**
   * Метод устанавливает текущее значение счетчика идентификатора.
   *
   * Метод использует систему кэширования, что позволяет реже обращаться к файловой системе для хранения счетчика идентификатора.
   * В случае, если кэширование отключено, метод работает с файловой системой при каждом обращении к нему.
   * @param integer $OID Новое значение счетчика идентификатора.
   */
  protected function set($OID){
    if($this->cache){
      $this->cache->Autoincrement_OID = $OID;

      // Автоматическая синхронизация каждые 10 минут.
      if(!isset($this->cache->Autoincrement_actual)){
        $this->cache->Autoincrement_actual = time();
      }
      elseif($this->cache->Autoincrement_actual+600 < time()){
        $this->synch();
      }
    }
    else{
      $this->conf->Autoincrement_OID = $OID;
    }
  }

  /**
   * Метод используется только при работе системы кэширования и позволяет синхронизировать значение кэша со значеним, хранящимся в файловой системе.
   */
  public function synch(){
    if($this->cache){
      if(isset($this->cache->Autoincrement_OID)){
        $this->conf->Autoincrement_OID = $this->cache->Autoincrement_OID;
      }
    }
  }

  /**
   * Метод генерирует новый идентификатор и возвращает его.
   * @return null|string Возвращает новый идентификатор или null - если файл инициализации поврежден и не удается найти идентифицирующее свойство.
   */
  public function generateOID(){
    $OID = $this->get();
    $this->set($OID+1);
    return $OID;
  }

  /**
   * Метод сбрасывает счетчик.
   */
  public function resetOID(){
    $this->set(1);
  }

  /**
   * Метод устанавливает счетчик в заданное значение.
   * @param integer $OID Новое значение счетчика.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если аргумент имеет неверный тип или меньше 1.
   */
  public function setOID($OID){
    \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException::verifyType($OID, 'i');
    \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException::verifyVal($OID, 'i > 0');
    $this->set($OID);
  }
}
