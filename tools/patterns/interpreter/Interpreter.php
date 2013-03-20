<?php
namespace PPHP\tools\patterns\interpreter;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Классы, реализующие данный интерфейс объединяются в шаблоне "Интерпретатор".
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\interpreter
 */
interface Interpreter{
  /**
   * Метод возвращает строку, полученную при интерпретации объекта.
   * @abstract
   *
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   *
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты объекта.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver = null);
}
