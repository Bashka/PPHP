<?php
namespace PPHP\tools\patterns\interpreter;

/**
 * Классы, реализующие данный интерфейс объединяются в шаблоне "Интерпретатор".
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\interpreter
 */
interface Interpreter{
  /**
   * Метод возвращает строку, полученную при интерпретации объекта.
   * @abstract
   * @param null|mixed $driver[optional] Данные, позволяющие изменить логику интерпретации объекта.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты объекта.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver = null);
}
