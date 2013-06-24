<?php
namespace PPHP\model\modules;

use PPHP\model\modules\SystemPackages\ReflectionModule;
use PPHP\services\modules\ModulesRouter;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\classes\standard\baseType\special\Name;
use PPHP\tools\classes\standard\fileSystem\NotExistsException;
use PPHP\tools\patterns\singleton\Singleton;
use PPHP\tools\patterns\singleton\TSingleton;

/**
 * Класс служит для разграничения доступа к контроллерам модулей.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\model\modules
 */
class AccessManager implements Singleton{
  use TSingleton;

  /**
   * Метод служит для определения доступности данного метода модуля текущему пользователю.
   * В случае отсутствия модуля для разграничения прав доступа, метод делает доступными все методы всех модулей.
   * @param string $module Проверяемый модуль.
   * @param string $active Запрашиваемый метод модуля.
   * @throws NotExistsException Выбрасывается в случае, если не удалось получить доступ к конфигурации системы или к модулю.
   * @return boolean true - если доступ разрешен, иначе - false.
   */
  public function isResolved($module, $active){
    try{
      $modulesRouter = ModulesRouter::getInstance();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
    if($modulesRouter->hasModule('Access')){
      try{
        $accessController = (new ReflectionModule('Access'))->getController();
      }
      catch(NotExistsException $e){
        throw $e;
      }

      return $accessController->isResolved(new Name($active), new Name($module));
    }
    else{
      return true;
    }
  }
}
