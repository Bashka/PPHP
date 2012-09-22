<?php
namespace PPHP\services\modules;

/**
 * Класс отвечает за регистрацию, удаление и предоставление модулей в роутере.
 */
class ModulesRouter implements ModulesRouterInterface, \PPHP\tools\patterns\singleton\Singleton{
use \PPHP\tools\patterns\singleton\TSingleton;

  /**
   * @var \PPHP\services\configuration\Configurator
   */
  protected $conf;

  private function __construct(){
    $this->conf = \PPHP\services\configuration\Configurator::getInstance();
  }

  /**
   * Метод возвращает контроллер для данного модуля.
   * @abstract
   * @param string $moduleName Имя модуля.
   * @return string Имя контроллера.
   */
  public function getController($moduleName){
    return $this->conf->get('ModulesRouter', $moduleName);
  }

  /**
   * Метод определяет, существует ли данный модуль в системе.
   * @abstract
   * @param string $moduleName Имя модуля.
   * @return boolean true - если модуль установлен, иначе - false.
   */
  public function isModuleExists($moduleName){
    return $this->conf->isExists('ModulesRouter', $moduleName);
  }

  /**
   * Метод добавляет новой путь в роутер.
   * @abstract
   * @param string $moduleName Имя модуля.
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionClass $controller Отображение класса контроллера для данного модуля.
   */
  public function setController($moduleName, \PPHP\tools\patterns\metadata\reflection\ReflectionClass $controller){
    $this->conf->set('ModulesRouter', $moduleName, '\\'.$controller->getName());
  }

  /**
   * Метод удаляет данные модуля из роутера.
   * @abstract
   * @param string $moduleName Имя модуля.
   * @return boolean true - если модуль был успешно удален из роутера, иначе - false.
   */
  public function removeController($moduleName){
    return $this->conf->delete('ModulesRouter', $moduleName);
  }

  /**
   * Метод возвращает имена всех зарегистрированных в системе модулей.
   * @return array Массив имен зарегистрированных в системе модулей.
   */
  public function getModulesNames(){
    $section = $this->conf->getSection('ModulesRouter');
    $modulesNames = [];
    foreach($section as $module => $controller){
      $modulesNames[] = $module;
    }
    return $modulesNames;
  }

  /**
   * Метод возвращает имена всех доступных методов контроллера модуля.
   * @param string $module Целевой модуль.
   * @return array|boolean Массив имен методов контроллера модуля или false - если заданного модуля не найдено.
   */
  public function getModuleActions($module){
    if(!$this->isModuleExists($module)){
      return false;
    }
    $controller = $this->getController($module);
    $controller = new \ReflectionClass($controller);
    $actions = [];
    foreach($controller->getMethods() as $action){
      if($action->isPublic() && $action->getDeclaringClass() == $controller){
        $actions[] = $action->getName();
      }
    }
    return $actions;
  }
}
