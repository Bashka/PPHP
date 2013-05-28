<?php
namespace PPHP\model\modules;

use PPHP\services\modules\ModulesRouter;
use PPHP\tools\patterns\singleton\Singleton;
use \PPHP\services\formatting\localisation as localisation;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;

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
   * @throws NotFoundDataException Выбрасывается в случае, если не удалось получить доступ к конфигурации системы или файлу состояния модуля.
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
          $localisationController = $modulesRouter->getController('Localisation');
        }
        catch(NotFoundDataException $e){
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
