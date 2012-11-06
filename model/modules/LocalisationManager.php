<?php
namespace PPHP\model\modules;

/**
 * Класс-фабрика, предоставляющий доступ к идентифицированному менеджеру локализации.
 */
class LocalisationManager implements \PPHP\tools\patterns\singleton\Singleton{
  /**
   * Текущий менеджер локализации.
   * @var \PPHP\services\formatting\localisation\LocalisationManager
   */
  protected static $instance;

  /**
   * Метод возвращает экземпляр менеджера локализации с устновленной текущей локалью пользователя по средствам модуля Localisation.
   * @static
   * @return \PPHP\services\formatting\localisation\LocalisationManager
   */
  static public function getInstance(){
    if(empty(self::$instance)){
      self::$instance = \PPHP\services\formatting\localisation\LocalisationManager::getInstance();
      $modulesRouter = \PPHP\services\modules\ModulesRouter::getInstance();
      if($modulesRouter->hasModule('Localisation')){
        $localisationController = $modulesRouter->getController('Localisation');
        self::$instance->setLocalise($localisationController->getLanguage());
      }
      else{
        self::$instance->setLocalise(\PPHP\services\formatting\localisation\LocalisationManager::getDefaultLanguage());
      }
    }
    return self::$instance;
  }
}
