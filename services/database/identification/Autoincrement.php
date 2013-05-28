<?php
namespace PPHP\services\database\identification;
use PPHP\services\cache\CacheAdapter;
use PPHP\services\cache\CacheSystem;
use PPHP\services\configuration\Configurator;
use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\patterns\database\identification\OIDGenerator;
use \PPHP\tools\patterns\singleton as singleton;
/**
 * Класс позволяет поддерживать неповторимость идентификатора по отношению к любому объекту в системе.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\services\database\identification
 */
class Autoincrement implements singleton\Singleton, OIDGenerator{
use singleton\TSingleton;

  /**
   * @var Configurator
   */
  protected $conf;

  /**
   * @var CacheAdapter
   */
  protected $cache;

  /**
   * @throws NotFoundDataException Выбрасывается в случае, если не удалось получить доступ к конфигурации системы.
   */
  private function __construct(){
    try{
      $this->conf = Configurator::getInstance();
      $this->cache = CacheSystem::getInstance();
    }
    catch(NotFoundDataException $e){
      throw new NotFoundDataException('Не удалось получить доступ к конфигурации системы.', 1, $e);
    }
  }

  /**
   * Метод возвращает текущее значение счетчика идентификатора.
   * Метод использует систему кэширования, что позволяет реже обращаться к файловой системе для хранения счетчика идентификатора.
   * В случае, если кэширование отключено, метод работает с файловой системой при каждом обращении к нему.
   * @return string|null Текущее значение счетчика идентификатора или null - если файл конфигурации поврежден.
   */
  protected function get(){
    $OID = null;
    if(CacheSystem::hasCache()){
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
   * Метод использует систему кэширования, что позволяет реже обращаться к файловой системе для хранения счетчика идентификатора.
   * В случае, если кэширование отключено, метод работает с файловой системой при каждом обращении к нему.
   * @param integer $OID Новое значение счетчика идентификатора.
   * @throws InvalidArgumentException Выбрасывается в случае, если аргумент имеет неверный тип или меньше 1.
   */
  protected function set($OID){
    InvalidArgumentException::verifyType($OID, 'i');
    InvalidArgumentException::verifyVal($OID, 'i > 0');
    if(CacheSystem::hasCache()){
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
    if(CacheSystem::hasCache()){
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
}
