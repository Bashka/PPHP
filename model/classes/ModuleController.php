<?php
namespace PPHP\model\classes;

/**
 * Все конкретные контроллеры модулей должны реализовывать данный интерфейс.
 */
abstract class ModuleController implements \PPHP\tools\patterns\metadata\reflection\Reflect, \PPHP\tools\patterns\singleton\Singleton{
use \PPHP\tools\patterns\metadata\reflection\TReflect;
use \PPHP\tools\patterns\singleton\TSingleton;
}
