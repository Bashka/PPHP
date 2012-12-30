<?php
namespace PPHP\tools\patterns\interpreter;

/**
 * Объект данного класса могут быть восстановлены из строки.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\interpreter
 */
interface Restorable{
  /**
   * Метод восстанавливает объект из строки.
   * @abstract
   * @param string $string Исходная строка.
   * @param null|mixed $driver[optional] Данные для восстановления.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты строки.
   * @return mixed Результирующий объект.
   */
  public static function reestablish($string, $driver = null);
}
