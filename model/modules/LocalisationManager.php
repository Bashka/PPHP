<?php
namespace PPHP\model\modules;

use PPHP\model\modules\SystemPackages\ReflectionModule;
use PPHP\services\formatting\localisation as localisation;
use PPHP\services\modules\ModulesRouter;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\classes\standard\fileSystem\NotExistsException;
use PPHP\tools\patterns\singleton\Singleton;

/**
 * Класс-фабрика, предоставляющий доступ к идентифицированному менеджеру локализации.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\model\modules
 */
class LocalisationManager implements Singleton{
  /**
   * Текущий менеджер локализации.
   * @var localisation\LocalisationManager
   */
  protected static $instance;

  /**
   * Метод возвращает экземпляр менеджера локализации с устновленной текущей локалью пользователя по средствам модуля Localisation.
   * @static
   * @throws NotExistsException Выбрасывается в случае, если не удалось получить доступ к конфигурации системы или к модулю.
   * @return localisation\LocalisationManager
   */
  static public function getInstance(){
    if(empty(self::$instance)){
      self::$instance = localisation\LocalisationManager::getInstance();
      try{
        $modulesRouter = ModulesRouter::getInstance();
      }
      catch(NotFoundDataException $e){
        throw $e;
      }
      if($modulesRouter->hasModule('Localisation')){
        try{
          $localisationController = (new ReflectionModule('Localisation'))->getController();
        }
        catch(NotExistsException $e){
          throw $e;
        }
        self::$instance->setLocalise($localisationController->getLanguage());
      }
      else{
        try{
          self::$instance->setLocalise(localisation\LocalisationManager::getDefaultLanguage());
        }
        catch(NotFoundDataException $e){
          throw $e;
        }
      }
    }

    return self::$instance;
  }
}
