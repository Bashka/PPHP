<?php
namespace PPHP\tools\patterns\database\identification;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

// @todo: Пересмотреть документацию класса для приведения к стандарту.
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
   * Метод возвращает фиктивный (proxy), идентифицированный объект.
   * Фиктивный объект, возвращаемый данным методом, не имеет состояния, но идентифицирован по средствам установки указанного целочисленного идентификатора.
   * Такого рода объект может быть использован как объектная ссылка на свое состояние для последующего восстановления.
   * Данная реализация использует конструктор класса не передавая ему параметров, это может привести к ошибке в случае, если конструктор ожидает параметры при вызове. Для решения этой проблемы следует переопределить метод.
   * @static
   * @param integer $OID Идентификатор объекта.
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа или недопустимого значения.
   * @return static Фиктивный (proxy) объект.
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
   * Метод возвращает идентификатор объекта.
   * @return integer|null Идентификатор объекта или null - если объект не идентифицирован.
   */
  public final function getOID(){
    return $this->OID;
  }

  /**
   * Метод устанавливает идентификатор для не идентифицированного объекта.
   * @param integer $OID Идентификатор объекта.
   * @throws OIDException Выбрасывается при передаче в качестве параметра уже идентифицированного объекта.
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа или недопустимого значения.
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
   * Метод проверяет, идентифицирован ли объект.
   * @return boolean true - если объект идентифицирован, иначе - false
   */
  public final function isOID(){
    return !is_null($this->OID);
  }
}
