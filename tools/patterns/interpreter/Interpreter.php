<?php
namespace PPHP\tools\patterns\interpreter;

use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;

/**
 * Объекты классов, реализующие данный интерфейс, могут быть интерпретированы в строку.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\interpreter
 */
interface Interpreter{
  /**
   * Метод возвращает строку, полученную при интерпретации вызываемого объекта.
   * @abstract
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации объекта.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае отсутствия требуемых для интерпретации данных.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver = null);
}
