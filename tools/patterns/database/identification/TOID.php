<?php
namespace PPHP\tools\patterns\database\identification;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Классическая реализация интерфейса OID
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\identification
 */
trait TOID{
  /**
   * Целочисленный идентификатор объекта или null - если объект не идентифицирован.
   * @var integer|null
   */
  protected $OID;

  /**
   * Метод возвращает идентификатор объекта.
   * @return integer|null Идентификатор объекта или null - если объект не идентифицирован.
   */
  public function getOID(){
    return $this->OID;
  }

  /**
   * Метод устанавливает идентификатор нового объекта.
   *
   * @param integer $OID Идентификатор объекта.
   *
   * @throws UpdatingOIDException Выбрасывается при попытке изменения идентификатора.
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  public function setOID($OID){
    if(!empty($this->OID)){
      throw new UpdatingOIDException();
    }
    $OID = (integer)$OID;
    if(!is_integer($OID)){
      throw new exceptions\InvalidArgumentException('integer', $OID);
    }
    $this->OID = $OID;
  }

  /**
   * Метод проверяет, идентифицирован ли объект.
   * @return boolean true - если объект идентифицирован, иначе - false
   */
  public function isOID(){
    return !is_null($this->getOID());
  }

  /**
   * Метод возвращает ссылку на объект в виде строки.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если на момент вызова метода объект не был идентифицирован.
   * @return string Ссылка формата $ИмяКласса:ЗначениеИдентификатора.
   */
  public function getLinkOID(){
    if(!$this->isOID()){
      throw new exceptions\NotFoundDataException('Невозможно получить строковую ссылку на неидентифицированный объект.');
    }
    return '$/' . str_replace('\\', '/', get_class($this)) . ':' . $this->getOID();
  }

  /**
   * Метод возвращает фиктивный (proxy), идентифицированный объект.
   *
   * Фиктивный объект, возвращаемый данным методом, не имеет состояния, но идентифицирован по средствам установки указанного целочисленного идентификатора.
   * Такого рода объект может быть использован как объектная ссылка на свое состояние для последующего восстановления.
   * @static
   *
   * @param integer $OID Идентификатор объекта.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return static Фиктивный (proxy) объект.
   */
  public static function getProxy($OID){
    $OID = (integer)$OID;
    if(!is_integer($OID)){
      throw new exceptions\InvalidArgumentException('integer', $OID);
    }
    $proxy = new static;
    $proxy->setOID($OID);
    return $proxy;
  }
}
