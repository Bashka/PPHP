<?php
namespace PPHP\model\modules\SystemPackages\InstallerModules;

use PPHP\model\modules\SystemPackages as sp;
use PPHP\services\log\LogManager;
use PPHP\services\modules\ModuleNotFoundException;
use PPHP\services\modules\ModulesRouter;
use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\classes\standard\baseType\exceptions\PDOException;
use PPHP\tools\classes\standard\baseType\exceptions\RuntimeException;
use PPHP\tools\classes\standard\baseType\Integer;
use PPHP\tools\classes\standard\baseType\special\Alias;
use PPHP\tools\classes\standard\baseType\special\Name;
use PPHP\tools\classes\standard\fileSystem\ComponentFileSystem;
use PPHP\tools\classes\standard\storage\database\UncertaintyException;

/**
 * Класс расширяет стандартное отражения модуля для добавления возможностей их удаления из системы.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\model\modules\SystemPackages\InstallerModules
 */
class ReflectionModule extends sp\ReflectionModule{
  /**
   * Метод удаляет информацию о модуле из маршрутизатора.
   * @throws NotFoundDataException Выбрасывается в случае отсутствия доступа к конфигурации системы.
   */
  public function removeRouter(){
    try{
      $router = ModulesRouter::getInstance();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
    $router->removeModule($this->getName());
  }

  /**
   * Метод оповещает родительский модуль об удалении его дочернего модуля удаляя информацию о нем из его файла состояния.
   * @throws ModuleNotFoundException Выбрасывается в случае отсутствия доступа к файлу состояния родительского модуля.
   */
  public function sayParent(){
    $parent = $this->getParent();
    if($parent !== false){
      try{
        (new sp\ReflectionModule($parent))->removeChild($this->getName());
      }
      catch(RuntimeException $e){
        throw new ModuleNotFoundException('Отсутствует доступ к файлу состояния родительского модуля [' . $parent . '].', 1, $e);
      }
      // Дальнейший выброс исключений не предполагается
    }
  }

  /**
   * Методо оповещает используемые модули об удалении зависимого модуля удаляя информацию о нем из их файлов состояния.
   * @param boolean $lazy [optional] Метод оповещения используемых модулей. false - сообщать об ошибке при отсутствии одного из используемых модулей, true - продолжать оповещение при отсутствии используемых модулей.
   * @throws ModuleNotFoundException Выбрасывается в случае отсутствия используемого модуля или доступа к его файлу состояния.
   * @throws InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   */
  public function sayUsed($lazy = false){
    InvalidArgumentException::verifyType($lazy, 'b');
    $used = $this->getUsed();
    if($used !== false){
      foreach($used as $useModule){
        try{
          (new sp\ReflectionModule($useModule))->removeDestitute($this->getName());
        }
        catch(ModuleNotFoundException $e){
          if(!$lazy){
            throw new ModuleNotFoundException('Отсутствует используемый модуль [' . $useModule . '].', 1, $e);
          }
        }
        catch(RuntimeException $e){
          if(!$lazy){
            throw new ModuleNotFoundException('Отсутствует доступ к файлу состояния используемого модуля [' . $useModule . '].', 1, $e);
          }
        }
      }
    }
  }

  /**
   * Метод удаляет связанные с данным модулем права доступа.
   * @throws NotFoundDataException Выбрасывается в случае отсутствия доступа к конфигурации системы.
   * @throws ModuleNotFoundException Выбрасывается в случае отсутствия модуля Access, обязательного для реализации прав доступа.
   */
  public function removeAccess(){
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
        // Получение права доступа
        try{
          $rule = $accessController->getOIDRule(new Name($name), new Name($method));
        }
        catch(UncertaintyException $e){
          continue;
        }
        catch(PDOException $e){
          throw $e;
        }
        // Удаления права доступа
        $accessController->removeRule(new Integer($rule));
      }
    }
    else{
      throw new ModuleNotFoundException('Отсутствует доступ к используемому модулю [Access].');
    }
  }

  /**
   * Метод удаляет каталог данного модуля из системы и выполняет внутреннюю деинсталляцию модуля.
   * Следует очень осторожно относиться к работе внутреннего инсталлятора, так как ошибки при его выполнеии не позволят полноценно удалить модуль и откатить изменения.
   * @return string Информация о результатах удаления модуля и ответ внутреннего инсталлятора.
   */
  public function uninstall(){
    $result = 'The module [' . $this->getName() . '] is removed. Installer: ';
    // Выполнение внутреннего деинсталлятора
    if(($installer = $this->getInstaller()) !== false){
      $result .= $installer->uninstall(); // Выброс исключений не предполагается
    }
    // Удаление каталога модуля
    ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . substr($this->getAddress(), 0, -1))->delete(); // Выброс исключений не предполагается
    return $result;
  }
}