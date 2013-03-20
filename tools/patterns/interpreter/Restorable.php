<?php
namespace PPHP\tools\patterns\interpreter;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Объект данного класса могут быть восстановлены из строки.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\interpreter
 */
interface Restorable{
  /**
   * Метод восстанавливает объект из строки.
   * @abstract
   *
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   *
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты строки.
   * @throws exceptions\StructureException Выбрасывается в случае, если исходная строка не отвечает требования структуры.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return mixed Результирующий объект.
   */
  public static function reestablish($string, $driver = null);
}
