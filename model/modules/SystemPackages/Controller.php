<?php
namespace PPHP\model\modules\SystemPackages;

use PPHP\model\classes\ModuleController;
use PPHP\services\modules\ModulesRouter;
use PPHP\services\view\ViewRouter;
use PPHP\tools\classes\standard\baseType\special\Name;

/**
 * Модуль определяет компоненты системы, такие как модули и экраны, а так же их архивы.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\model\modules\SystemPackages
 */
class Controller extends ModuleController{
  /**
   * Метод определяет наличие указанного модуля в системе.
   * @param Name $name Искомый модуль.
   * @return boolean true - если модуль установлен, иначе - false.
   */
  public function hasModule(Name $name){
    return ModulesRouter::getInstance()->hasModule($name->getVal());
  }
  /**
   * Метод определяет наличие указанного экрана в системе.
   * @param Name $module Целевой модуль.
   * @param Name $screen Целевой экран.
   * @return boolean true - если экран установлен, иначе - false.
   */
  public function hasScreen(Name $module, Name $screen){
    return ViewRouter::getInstance()->hasScreen($module->getVal(), $screen->getVal());
  }
}
