<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\interpreter as interpreter;

/**
 * Классы, наследующие поведение от данного класса являются объектными SQL инструкциями или компонентами.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
abstract class ComponentQuery implements interpreter\Interpreter, interpreter\Restorable{
  use interpreter\TRestorable;

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function updateString(&$string){
    $string = preg_replace('/(\n|\t|\r)/u', '', $string);
    $string = preg_replace('/(  +)/u', ' ', $string);
  }
}
