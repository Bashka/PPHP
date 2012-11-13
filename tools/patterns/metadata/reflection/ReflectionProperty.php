<?php
namespace PPHP\tools\patterns\metadata\reflection;

/**
 * Представление свойства класса, расширенное возможностью добавления метаданных.
 *
 * Данный класс является отображением свойства с устойчивым состоянием и возможностью аннотирования.
 * Класс наследует все возможности своего родителя, что позволяет использовать его в контексте родительского класса.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\metadata\reflection
 */
class ReflectionProperty extends \ReflectionProperty implements \PPHP\tools\patterns\metadata\Described{
use \PPHP\tools\patterns\metadata\TDescribed;
}
