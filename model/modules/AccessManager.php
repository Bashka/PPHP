<?php
namespace PPHP\model\modules;

/**
 * Класс служит для разграничения доступа к контроллерам модулей.
 */
class AccessManager implements \PPHP\tools\patterns\singleton\Singleton{
  use \PPHP\tools\patterns\singleton\TSingleton;

  /**
   * Метод служит для определения доступности данного метода модуля текущему пользователю.
   * В случае отсутствия модуля для разграничения прав доступа, метод делает доступными все методы всех модулей.
   * @param string $module Проверяемый модуль.
   * @param string $active Запрашиваемый метод модуля.
   * @return boolean true - если доступ разрешен, иначе - false.
   */
  public function isResolved($module, $active){
    $modulesRouter = \PPHP\services\modules\ModulesRouter::getInstance();
    if($modulesRouter->hasModule('Access')){
      $accessController = $modulesRouter->getController('Access');
      return $accessController->isResolved(new \PPHP\tools\classes\standard\baseType\special\Name($module), new \PPHP\tools\classes\standard\baseType\special\Name($active));
    }
    else{
      return true;
    }
  }
}
