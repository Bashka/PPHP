<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\interpreter as interpreter;

/**
 * Классы, реализующие данный класс являются частью унифицированной SQL инструкции.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
abstract class ComponentQuery implements interpreter\Interpreter, interpreter\Restorable{
  use interpreter\TRestorable;

  public static function updateString(&$string){
    $string = preg_replace('/(\n|\t|\r)/u', '', $string);
    $string = preg_replace('/(  +)/u', ' ', $string);
  }
}
