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

  /**
   * Данный метод вызывается перед вызовом метода контроллера центральным контроллером.
   * Метод может быть переопределен в дочерних классах для его конкретизации.
   */
  public function afterRun(){
  }

  /**
   * Данный метод вызывается после вызова метода контроллера центральным контроллером.
   * Метод может быть переопределен в дочерних классах для его конкретизации.
   */
  public function beforeRun(){
  }
}
