<?php
namespace PPHP\services\modules;

/**
 * Класс отвечает за регистрацию, удаление и предоставление модулей в роутере.
 */
class ModulesRouter implements \PPHP\tools\patterns\singleton\Singleton{
use \PPHP\tools\patterns\singleton\TSingleton;

  /**
   * Адрес хранилища модулей.
   */
  const MODULES_DIR = 'PPHP/model/modules';

  /**
   * Конфигуратор системы.
   * @var \PPHP\services\configuration\Configurator
   */
  protected $conf;
  /**
   * @var \PPHP\services\cache\CacheAdapter
   */
  protected $cache;

  /**
   * Множество инициированных отражений модулей.
   * @var \PPHP\tools\patterns\metadata\reflection\ReflectionModule[]
   */
  protected $reflectionsModules = [];

  private function __construct(){
    $this->conf = \PPHP\services\configuration\Configurator::getInstance();
    $this->cache = \PPHP\services\cache\CacheSystem::getInstance();
    if(\PPHP\services\cache\CacheSystem::hasCache() && !isset($this->cache->ModulesRouter_Init)){
      $modulesNames = $this->getModulesNames();
      foreach($modulesNames as $moduleName){
        $this->cache->set('ModulesRouter_Modules_'.$moduleName, $this->conf->get('Modules', $moduleName));
      }
      $this->cache->ModulesRouter_Init = 1;
    }
  }

  /**
   * Метод возвращает расположение модуля относительно хранилища модулей.
   * @param string $moduleName Имя модуля.
   * @throws ModuleNotFoundException Выбрасывается в случае, если треубемый модуль не устноалвен.
   * @return string Относительное расположение модуля вида: [имяРодителькогоМодуля/]имяМодуля.
   */
  public function getModule($moduleName){
    $module = null;
    if(($module = $this->cache->get('ModulesRouter_Modules_'.$moduleName)) === false){
      if(!$this->conf->isExists('Modules', $moduleName)){
        throw new ModuleNotFoundException($moduleName);
      }
      $module = $this->conf->get('Modules', $moduleName);
      $this->cache->set('ModulesRouter_Modules_'.$moduleName, $module);
    }
    return $module;
  }

  /**
   * Метод возвращает отражение указанного модуля.
   * @param string $moduleName Целевой модуль.
   * @return \PPHP\tools\patterns\metadata\reflection\ReflectionModule Отражение модуля.
   */
  public function &getReflectionModule($moduleName){
    if(!isset($this->reflectionsModules[$moduleName])){
      $moduleLocation = $this->getModule($moduleName);
      $this->reflectionsModules[$moduleName] = new \PPHP\tools\patterns\metadata\reflection\ReflectionModule($moduleName, $moduleLocation, self::MODULES_DIR.'/'.$moduleLocation);
    }
    return $this->reflectionsModules[$moduleName];
  }

  /**
   * Метод возвращает контроллер конкретного модуля.
   * @param string $moduleName Имя целевого модуля.
   * @return \PPHP\model\classes\ModuleController Контроллер модуля.
   */
  public function getController($moduleName){
    $reflectionModule = $this->getReflectionModule($moduleName);
    return $reflectionModule->getController();
  }

  /**
   * Метод определяет, существует ли данный модуль в системе.
   * @param string $moduleName Имя модуля.
   * @return boolean true - если модуль установлен, иначе - false.
   */
  public function hasModule($moduleName){
    if($this->cache->get('ModulesRouter_Modules_'.$moduleName) === false){
      return $this->conf->isExists('Modules', $moduleName);
    }
    return true;
  }

  /**
   * Метод добавляет новой модуль в роутер.
   * @param string $moduleName Имя модуля.
   * @param string $parentModule Имя родительского модуля.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Вырасывается в случае, если добавляемый модуль уже присутствует в системе.
   */
  public function addModule($moduleName, $parentModule = null){
    if($this->hasModule($moduleName)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('Указанный модуль ' . $moduleName . ' уже установлен в системе.');
    }
    if(!is_null($parentModule)){
      $addressModuleDir = $this->getModule($parentModule) . '/';
    }
    else{
      $addressModuleDir = '';
    }
    $addressModuleDir .= $moduleName;
    $this->conf->set('Modules', $moduleName, $addressModuleDir);
    $this->cache->set('ModulesRouter_Modules_'.$moduleName, $addressModuleDir);
  }

  /**
   * Метод удаляет данные модуля из роутера.
   * @param string $moduleName Имя модуля.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если целевой модуль не установлен в системе.
   * @return boolean true - если модуль был успешно удален из роутера, иначе - false.
   */
  public function removeModule($moduleName){
    if(!$this->hasModule($moduleName)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('Указанный модуль ' . $moduleName . ' не установлен в системе.');
    }
    $this->cache->remove('ModulesRouter_Modules_'.$moduleName);
    return $this->conf->delete('Modules', $moduleName);
  }

  /**
   * Метод возвращает имена всех зарегистрированных в системе модулей.
   * @return array Массив имен зарегистрированных в системе модулей.
   */
  public function getModulesNames(){
    $section = $this->conf->getSection('Modules');
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
    if(!$this->hasModule($module)){
      return false;
    }
    $controller = $this->getController($module)->getReflectionClass();
    $actions = [];
    foreach($controller->getMethods() as $action){
      if($action->isPublic() && $action->getDeclaringClass()->getName() == $controller->getName()){
        $actions[] = $action->getName();
      }
    }
    return $actions;
  }
}
