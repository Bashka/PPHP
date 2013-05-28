<?php
namespace PPHP\model\classes;
use PPHP\tools\patterns\metadata\reflection\Reflect;
use PPHP\tools\patterns\metadata\reflection\TReflect;
use PPHP\tools\patterns\singleton\Singleton;
use PPHP\tools\patterns\singleton\TSingleton;

/**
 * Класс является родительским по отношению ко всем контроллерам модулей.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\model\classes
 */
abstract class ModuleController implements Reflect, Singleton{
use TReflect;
use TSingleton;
}
