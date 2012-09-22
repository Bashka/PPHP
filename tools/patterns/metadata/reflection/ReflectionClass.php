<?php
namespace PPHP\tools\patterns\metadata\reflection;

/**
 * Отражение класса, расширенное возможностью добавления метаданных.
 */
class ReflectionClass extends \ReflectionClass implements \PPHP\tools\patterns\metadata\Described{
use \PPHP\tools\patterns\metadata\TDescribed;
}
