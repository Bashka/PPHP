<?php
namespace PPHP\tools\patterns\metadata\reflection;

/**
 * Отражение метода класса, расширенное возможностью добавления метаданных.
 */
class ReflectionMethod extends \ReflectionMethod implements \PPHP\tools\patterns\metadata\Described{
use \PPHP\tools\patterns\metadata\TDescribed;
}