<?php
namespace PPHP\tools\patterns\database\identification;

/**
 * Данный интерфейс реализуется классами, экземпляры которых имеют ключевой идентификатор. Идентификатор может быть присвоен классу только один раз, попытка изменения идентификатора приведет к ошибке.
 */
interface OID{
  /**
   * Метод возвращает идентификатор объекта.
   * @abstract
   * @return integer
   */
  public function getOID();

  /**
   * Метод устанавливает идентификатор нового объекта.
   * @abstract
   * @param integer $OID
   * @throws UpdatingOIDException Выбрасывается при попытке изменения идентификатора.
   * @return void
   */
  public function setOID($OID);

  /**
   * Метод проверяет, определен ли идентификатор для объекта.
   * @abstract
   * @return boolean true - если идентификатор определен, иначе - false
   */
  public function isOID();

  /**
   * Метод возвращает ссылку на объект в виде строки.
   * @abstract
   * @return string Ссылка формата $ИмяКласса:OID
   */
  public function getLinkOID();

  /**
   * Метод возвращает фиктивный объект, готовый к восстановлению по OID.
   * @static
   * @abstract
   * @param integer $OID Идентификатор объекта.
   * @return static
   */
  public static function getProxy($OID);
}
