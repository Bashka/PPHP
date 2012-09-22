<?php
namespace PPHP\tools\patterns\metadata\reflection;

/**
 * Представление свойства класса, расширенное возможностью добавления метаданных.
 */
class ReflectionProperty extends \ReflectionProperty implements \PPHP\tools\patterns\metadata\Described{
use \PPHP\tools\patterns\metadata\TDescribed;
}
