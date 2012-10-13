<?php
namespace PPHP\tools\patterns\metadata\reflection;

/**
 * Представление параметра метода, расширенное возможностью добавления метаданных.
 */
class ReflectionParameter extends \ReflectionParameter implements \PPHP\tools\patterns\metadata\Described{
use \PPHP\tools\patterns\metadata\TDescribed;
}
