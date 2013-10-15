<?php
namespace PPHP\tools\patterns\interpreter;

use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\classes\standard\baseType\exceptions\StructureException;

/**
 * Объекты классов, реализующие данный интерфейс, могут быть восстановлены из строки.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\interpreter
 */
interface Restorable{
  /**
   * Метод позволяет определить допустимость восстановления объекта из строки-основания.
   * @abstract
   * @param string $string Строка-основание.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return boolean true - если интерпретация возможна, иначе - false.
   */
  public static function isReestablish($string, $driver = null);

  /**
   * Метод восстанавливает объект из строки-основания.
   * @abstract
   * @param string $string Строка-основание.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации строки-основания.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае отсутствия требуемых для восстановления данных.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\StructureException Выбрасывается в случае, если строка-основание не отвечает требования структуры.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return static Восстановленный объект.
   */
  public static function reestablish($string, $driver = null);
}