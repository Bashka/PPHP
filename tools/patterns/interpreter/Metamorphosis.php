<?php
namespace PPHP\tools\patterns\interpreter;

use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;

/**
 * Объекты классов, реализующие данный интерфейс, могут быть восстановлены из других объектов.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\interpreter
 */
interface Metamorphosis{
  /**
   * Метод восстанавливает вызываемый объект из другого объекта.
   * @abstract
   * @param Object $object Объект-основание.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае отсутствия требуемых для восстановления данных.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return static Результирующий объект.
   */
  public static function metamorphose($object, $driver = null);
}