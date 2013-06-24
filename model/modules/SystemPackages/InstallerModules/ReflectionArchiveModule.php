<?php
namespace PPHP\model\modules\SystemPackages\InstallerModules;

use PPHP\model\modules\SystemPackages as sp;
use PPHP\model\modules\SystemPackages\ReflectionModule;
use PPHP\services\modules\ModuleNotFoundException;
use PPHP\services\modules\ModulesRouter;
use PPHP\tools\classes\standard\baseType\exceptions\DuplicationException;
use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\classes\standard\baseType\exceptions\RuntimeException;
use PPHP\tools\classes\standard\baseType\Integer;
use PPHP\tools\classes\standard\baseType\special\Alias;
use PPHP\tools\classes\standard\baseType\special\Name;
use PPHP\tools\classes\standard\fileSystem\ComponentFileSystem;

/**
 * Класс расширяет стандартное отражения архива модуля для добавления возможностей их установки в систему.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\model\modules\SystemPackages\InstallerModules
 */
class ReflectionArchiveModule extends sp\ReflectionArchiveModule{
  /**
   * Метод определяет, установлен ли данный модуль в системе.
   * @throws NotFoundDataException Выбрасывается в случае отсутствия доступа к конфигурации системы.
   * @return boolean true - если модуль с данным именем уже установлен в системе, иначе - false,
   */
  public function isDuplication(){
    try{
      $router = ModulesRouter::getInstance();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }

    return $router->hasModule($this->getName());
  }

  /**
   * Метод добавляет информацию о модуле в роутер.
   * @throws NotFoundDataException Выбрасывается в случае отсутствия доступа к конфигурации системы.
   * @throws ModuleNotFoundException Выбрасывается в случае отсутствия родительского модуля.
   */
  public function addRouter(){
    try{
      $router = ModulesRouter::getInstance();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
    $parent = $this->getParent();
    try{
      $router->addModule($this->getName(), (($parent !== false)? $parent : null));
    }
    catch(ModuleNotFoundException $e){
      throw new ModuleNotFoundException('Родительский модуль [' . $parent . '] не установлен.', 1, $e);
    }
  }

  /**
   * Метод определяет, установлен ли родительский модуль в системе.
   * @throws NotFoundDataException Выбрасывается в случае отсутствия доступа к конфигурации системы.
   * @return boolean true - если родительский модуль установлен или у модуля нет родителя, иначе - false,
   */
  public function isParent(){
    try{
      $router = ModulesRouter::getInstance();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
    if($this->hasParent()){
      return $router->hasModule($this->getParent());
    }

    return true;
  }

  /**
   * Метод оповещает родительский модуль о появлении нового ребенка добавляя информацию о нем в его файл состояния.
   * @throws ModuleNotFoundException Выбрасывается в случае отсутствия доступа к файлу состояния родительского модуля.
   */
  public function sayParent(){
    $parent = $this->getParent();
    if($parent !== false){
      try{
        (new sp\ReflectionModule($parent))->addChild($this->getName());
      }
      catch(RuntimeException $e){
        throw new ModuleNotFoundException('Отсутствует доступ к файлу состояния родительского модуля [' . $parent . '].', 1, $e);
      }
      // Дальнейший выброс исключений не предполагается
    }
  }

  /**
   * Метод определяет, установлены ли все используемые модули в системе.
   * @throws NotFoundDataException Выбрасывается в случае отсутствия доступа к конфигурации системы.
   * @return boolean|string true - если все используемые модули установлены, иначе метод возвращает имя первого неустановленного модуля,
   */
  public function isUsed(){
    try{
      $router = ModulesRouter::getInstance();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
    if($this->hasUsed()){
      $used = $this->getUsed();
      foreach($used as $module){
        if(!$router->hasModule($module)){
          return $module;
        }
      }
    }

    return true;
  }

  /**
   * Методо оповещает используемые модули о появлении зависимого модуля добавляя информацию о нем в их файлы состояния.
   * @param boolean $lazy [optional] Метод оповещения используемых модулей. false - сообщать об ошибке при отсутствии одного из используемых модулей, true - продолжать оповещение при отсутствии используемых модулей.
   * @throws ModuleNotFoundException Выбрасывается в случае отсутствия используемого модуля или доступа к его файлу состояния.
   * @throws InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   */
  public function sayUsed($lazy = false){
    InvalidArgumentException::verifyType($lazy, 'b');
    $used = $this->getUsed();
    $name = $this->getName();
    if($used !== false){
      foreach($used as $module){
        try{
          (new sp\ReflectionModule($module))->addDestitute($name);
        }
        catch(ModuleNotFoundException $e){
          if(!$lazy){
            throw new ModuleNotFoundException('Отсутствует используемый модуль [' . $module . '].', 1, $e);
          }
        }
        catch(RuntimeException $e){
          if(!$lazy){
            throw new ModuleNotFoundException('Отсутствует доступ к файлу состояния используемого модуля [' . $module . '].', 1, $e);
          }
        }
      }
    }
  }

  /**
   * Метод применяет ограничения прав доступа определенного в файле конфигурации архива только в отношении существующих на момент вызова метода ролей.
   * @throws NotFoundDataException Выбрасывается в случае отсутствия доступа к конфигурации системы.
   * @throws ModuleNotFoundException Выбрасывается в случае отсутствия модуля Access, обязательного для реализации прав доступа.
   */
  public function addAccess(){
    try{
      $router = ModulesRouter::getInstance();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
    $name = $this->getName();
    if($router->hasModule('Access')){
      $accessController = (new ReflectionModule('Access'))->getController();
      foreach($this->getAccess() as $method => $roles){
        foreach($roles as $role){
          // Определения наличия указанной роли.
          if(($role = $accessController->getOIDRole(new Alias($role))) !== false){
            try{
              // Попытка добавление права доступа.
              $rule = $accessController->addRule(new Name($name), new Name($method));
            }
            catch(DuplicationException $exc){
              // В случае наличия добавляемого права доступа, получение его идентификатора.
              $rule = $accessController->getRuleFromPurpose(new Name($name), new Name($method))->getOID();
            }
            // Расширение роли указанным правом доступа.
            $accessController->expandRole(new Integer($role), new Integer($rule));
          }
        }
      }
    }
    else{
      throw new ModuleNotFoundException('Отсутствует доступ к используемому модулю [Access].');
    }
  }

  /**
   * Метод распаковывает данный архив в систему и выполняет внутренний инсталлятор.
   * @throws DuplicationException Выбрасывается в случае наличия каталога модуля в системе.
   * @return string Информация о результатах установки модуля и ответ внутреннего инсталлятора.
   */
  public function install(){
    $name = $this->getName();
    $result = 'The module [' . $name . '] is installed. Installer: ';
    // Распаковка архива
    if(($parent = $this->getParent()) !== false){
      $address = $_SERVER['DOCUMENT_ROOT'] . (new sp\ReflectionModule($parent))->getAddress(); // Выброс исключений не предполагается
    }
    else{
      $address = $_SERVER['DOCUMENT_ROOT'] . '/' . ModulesRouter::MODULES_DIR;
    }
    try{
      $this->expand(ComponentFileSystem::constructDirFromAddress($address));
    }
    catch(DuplicationException $e){
      throw $e;
    }
    // Выполнение внутреннего инсталлятора
    if($this->hasInstaller()){
      $result .= (new sp\ReflectionModule($name))->getInstaller()->install(); // Выброс исключений не предполагается
    }

    return $result;
  }
}