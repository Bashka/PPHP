<?php
namespace PPHP\services\modules;
use PPHP\model\classes\ModuleController;
use PPHP\services\cache\CacheAdapter;
use PPHP\services\cache\CacheSystem;
use PPHP\services\configuration\Configurator;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\classes\standard\fileSystem\NotExistsException;
use PPHP\tools\patterns\metadata\reflection\ReflectionModule;
use \PPHP\tools\patterns\singleton as singleton;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
/**
 * Класс отвечает за регистрацию, удаление и предоставление модулей.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\services\modules
 */
class ModulesRouter implements singleton\Singleton{
use singleton\TSingleton;

  /**
   * Адрес хранилища модулей.
   */
  const MODULES_DIR = 'PPHP/model/modules';

  /**
   * Конфигуратор системы.
   * @var Configurator
   */
  protected $conf;
  /**
   * Кэш.
   * @var CacheAdapter
   */
  protected $cache;

  /**
   * Множество инициированных отражений модулей.
   * @var ReflectionModule[]
   */
  protected $reflectionsModules = [];

  /**
   * @throws NotFoundDataException Выбрасывается в случае, если не удалось получить доступ к конфигурации системы.
   */
  private function __construct(){
    try{
      $this->conf = Configurator::getInstance();
      $this->cache = CacheSystem::getInstance();
    }
    catch(NotFoundDataException $e){
      throw new NotFoundDataException('Не удалось получить доступ к конфигурации системы.', 1, $e);
    }

    // Заполнение кэша
    // Выброс исключений не предполагается
    if(CacheSystem::hasCache() && !isset($this->cache->ModulesRouter_Init)){
      $modulesNames = $this->getModulesNames();
      foreach($modulesNames as $moduleName){
        // Выброс исключений не предполагается
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
    // Выброс исключений не предполагается
    if(($module = $this->cache->get('ModulesRouter_Modules_'.$moduleName)) === null){
      if(!$this->conf->isExists('Modules', $moduleName)){
        throw ModuleNotFoundException::getException($moduleName);
      }
      $module = $this->conf->get('Modules', $moduleName);
      $this->cache->set('ModulesRouter_Modules_'.$moduleName, $module);
    }
    return $module;
  }

  /**
   * Метод возвращает отражение указанного модуля.
   * @param string $moduleName Целевой модуль.
   * @throws NotFoundDataException Выбрасывается в случае, если не удалось получить доступ к файлу состояния модуля.
   * @return ReflectionModule Отражение модуля.
   */
  public function &getReflectionModule($moduleName){
    if(!isset($this->reflectionsModules[$moduleName])){
      $moduleLocation = $this->getModule($moduleName);
      try{
        $this->reflectionsModules[$moduleName] = new ReflectionModule($moduleName, $moduleLocation, self::MODULES_DIR.'/'.$moduleLocation);
      }
      catch(NotExistsException $e){
        throw new NotFoundDataException('Отсутствует доступ к файлу состояния модуля.', 1, $e);
      }
      catch(exceptions\RuntimeException $e){
        throw new NotFoundDataException('Отсутствует доступ к файлу состояния модуля.', 1, $e);
      }

    }
    return $this->reflectionsModules[$moduleName];
  }

  /**
   * Метод возвращает контроллер конкретного модуля.
   * @param string $moduleName Имя целевого модуля.
   * @throws NotFoundDataException Выбрасывается в случае, если не удалось получить доступ к файлу состояния модуля.
   * @throws exceptions\RuntimeException Выбрасывается в случае, если модуль не является конкретным.
   * @return ModuleController Контроллер модуля.
   */
  public function getController($moduleName){
    try{
      $reflectionModule = $this->getReflectionModule($moduleName);
    }
    catch(NotFoundDataException $e){
      throw $e;
    }

    try{
      return $reflectionModule->getController();
    }
    catch(exceptions\RuntimeException $e){
      throw $e;
    }
  }

  /**
   * Метод определяет, существует ли данный модуль в системе.
   * @param string $moduleName Имя модуля.
   * @return boolean true - если модуль установлен, иначе - false.
   */
  public function hasModule($moduleName){
    // Выброс исключений не предполагается
    if($this->cache->get('ModulesRouter_Modules_'.$moduleName) === null){
      return $this->conf->isExists('Modules', $moduleName);
    }
    return true;
  }

  /**
   * Метод добавляет новой модуль в роутер.
   * @param string $moduleName Имя модуля.
   * @param string $parentModule Имя родительского модуля.
   * @throws ModuleDuplicationException Вырасывается в случае, если добавляемый модуль уже присутствует в системе.
   * @throws ModuleNotFoundException Выбрасывается в случае отсутствия родительского модуля.
   */
  public function addModule($moduleName, $parentModule = null){
    if($this->hasModule($moduleName)){
      throw ModuleDuplicationException::getException($moduleName);
    }
    if(!is_null($parentModule)){
      try{
        $addressModuleDir = $this->getModule($parentModule) . '/';
      }
      catch(ModuleNotFoundException $e){
        throw $e;
      }
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
   * @throws ModuleNotFoundException Выбрасывается в случае, если целевой модуль не установлен в системе.
   * @return boolean true - если модуль был успешно удален из роутера, иначе - false.
   */
  public function removeModule($moduleName){
    if(!$this->hasModule($moduleName)){
      throw ModuleNotFoundException::getException($moduleName);
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
   * @throws NotFoundDataException Выбрасывается в случае, если не удалось получить доступ к файлу состояния модуля.
   * @throws exceptions\RuntimeException Выбрасывается в случае, если модуль не является конкретным.
   * @return array|boolean Массив имен методов контроллера модуля или false - если заданного модуля не найдено.
   */
  public function getModuleActions($module){
    if(!$this->hasModule($module)){
      return false;
    }
    try{
      $controller = $this->getController($module)->getReflectionClass();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
    catch(exceptions\RuntimeException $e){
      throw $e;
    }

    $actions = [];
    foreach($controller->getMethods() as $action){
      if($action->isPublic() && $action->getDeclaringClass()->getName() == $controller->getName()){
        $actions[] = $action->getName();
      }
    }
    return $actions;
  }
}
