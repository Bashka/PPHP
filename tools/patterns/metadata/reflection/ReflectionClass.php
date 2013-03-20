<?php
namespace PPHP\tools\patterns\metadata\reflection;
use \PPHP\tools\patterns\metadata as metadata;

/**
 * Отражение класса, расширенное возможностью добавления метаданных.
 *
 * Данный класс является отображением класса с устойчивым состоянием и возможностью аннотирования.
 * Класс наследует все возможности своего родителя, что позволяет использовать его в контексте родительского класса.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\metadata\reflection
 */
class ReflectionClass extends \ReflectionClass implements metadata\Described{
  use metadata\TDescribed;
}
