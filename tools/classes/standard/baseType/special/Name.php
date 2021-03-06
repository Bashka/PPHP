<?php
namespace PPHP\tools\classes\standard\baseType\special;

use \PPHP\tools\classes\standard\baseType as baseType;
use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс-обертка служит для представления и верификации имен.
 * Допустимый тип: только латинские буквы любого регистра, знак подчеркивания и цифры, но не на месте первого символа
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\special
 */
class Name extends baseType\Wrapper{
  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return ['[A-Za-z_][A-Za-z_0-9]*'];
  }

  /**
   * Метод восстанавливает объект из строки.
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws exceptions\StructureException Выбрасывается в случае, если исходная строка не отвечает требования структуры.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return static Результирующий объект.
   */
  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    parent::reestablish($string);

    return new self($string);
  }
}
