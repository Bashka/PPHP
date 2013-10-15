<?php
namespace PPHP\tools\patterns\database\identification;

/**
 * Данный интерфейс реализуется классами, состояния экземпляров которых могут быть идентифицированы по средствам объектного идентификатора.
 * Идентификатор может быть присвоен объекту только один раз, попытка изменения идентификатора приведет к ошибке. Это вызвано тем, что любой объект может быть идентифицирован только единожды, а смена идентификатора может привести к коллизии (наложению двух состояний на один объект).
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\identification
 */
interface OID{
  /**
   * Метод возвращает фиктивный (proxy) объект, имеющий объектный идентификатор.
   * Такого рода объект может быть использован как объектная ссылка на свое состояние для последующего восстановления.
   * @static
   * @abstract
   * @param integer $OID Идентификатор объекта.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа или недопустимого значения.
   * @return static Фиктивный (proxy) объект с установленным объектным идентификатором.
   */
  public static function getProxy($OID);

  /**
   * Метод возвращает объектный идентификатор вызываемого объекта.
   * @abstract
   * @return integer|null Идентификатор объекта или null - если объект не идентифицирован.
   */
  public function getOID();

  /**
   * Метод устанавливает идентификатор для не идентифицированного объекта.
   * @abstract
   * @param integer $OID Идентификатор объекта.
   * @throws \PPHP\tools\patterns\database\identification\OIDException Выбрасывается в случае, если вызываемый объект уже идентифицирован.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа или недопустимого значения.
   */
  public function setOID($OID);

  /**
   * Метод проверяет, идентифицирован ли объект.
   * @abstract
   * @return boolean true - если объект идентифицирован, иначе - false
   */
  public function isOID();
}
