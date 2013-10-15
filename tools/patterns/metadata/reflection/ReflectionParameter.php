<?php
namespace PPHP\tools\patterns\metadata\reflection;

use PPHP\tools\patterns\metadata as metadata;

/**
 * Отражение параметра метода, расширенное возможностью добавления метаданных.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\metadata\reflection
 */
class ReflectionParameter extends \ReflectionParameter implements metadata\Described{
  use metadata\TDescribed;
}
