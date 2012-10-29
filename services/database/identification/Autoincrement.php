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
   * @throws \PPHP\services\InitializingDataNotFoundException Выбрасывается в случае, если не удалось инициализировать соединение.
   */
  private function __construct(){
    $this->conf = \PPHP\services\configuration\Configurator::getInstance();
  }

  /**
   * Метод генерирует новый идентификатор и возвращает его.
   * @return null|string Возвращает новый идентификатор или null - если файл инициализации поврежден и не удается найти идентифицирующее свойство
   */
  public function generateOID(){
    $OID = $this->conf->get('Autoincrement', 'OID');
    $this->conf->set('Autoincrement', 'OID', $OID + 1);
    return $OID;
  }

  /**
   * Метод сбрасывает счетчик.
   */
  public function resetOID(){
    $this->conf->set('Autoincrement', 'OID', '1');
  }

  /**
   * Метод устанавливает счетчик в заданное значение.
   * @param integer $OID Новое значение счетчика.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если аргумент имеет неверный тип или меньше 1.
   */
  public function setOID($OID){
    if(!is_integer($OID) || $OID < 1){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('integer', $OID);
    }
    $this->conf->set('Autoincrement', 'OID', $OID);
  }
}
