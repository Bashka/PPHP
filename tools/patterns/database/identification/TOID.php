<?php
namespace PPHP\tools\patterns\database\identification;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Классическая реализация интерфейса OID
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\identification
 */
trait TOID{
  /**
   * @var integer|null Целочисленный идентификатор объекта или null - если объект не идентифицирован.
   */
  private $OID = null;

  /**
   * Данная реализация использует конструктор класса не передавая ему параметров, это может привести к ошибке в случае, если конструктор ожидает параметры при вызове. Для обхода этого ограничения можно переопределить метод.
   * @prototype \PPHP\tools\patterns\database\identification\OID
   */
  public static function getProxy($OID){
    exceptions\InvalidArgumentException::verifyType($OID, 'i');
    exceptions\InvalidArgumentException::verifyVal($OID, 'i > 0');
    /**
     * @var OID $proxy Proxy вызываемого объекта.
     */
    $proxy = new static;
    $proxy->setOID($OID);

    return $proxy;
  }

  /**
   * @prototype \PPHP\tools\patterns\database\identification\OID
   */
  public final function getOID(){
    return $this->OID;
  }

  /**
   * @prototype \PPHP\tools\patterns\database\identification\OID
   */
  public final function setOID($OID){
    if(!empty($this->OID)){
      throw new OIDException('Предотвращение коллизии.');
    }
    exceptions\InvalidArgumentException::verifyType($OID, 'i');
    exceptions\InvalidArgumentException::verifyVal($OID, 'i > 0');
    $this->OID = $OID;
  }

  /**
   * @prototype \PPHP\tools\patterns\database\identification\OID
   */
  public final function isOID(){
    return !is_null($this->OID);
  }
}
