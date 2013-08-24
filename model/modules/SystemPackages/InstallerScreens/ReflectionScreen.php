<?php
namespace PPHP\model\modules\SystemPackages\InstallerScreens;

use PPHP\model\modules\SystemPackages\SystemComponentNotFoundException;
use PPHP\model\modules\SystemPackages as sp;
use PPHP\services\view\ScreenNotFoundException;
use PPHP\services\view\ViewRouter;
use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\classes\standard\baseType\exceptions\RuntimeException;
use PPHP\tools\classes\standard\fileSystem\ComponentFileSystem;

/**
 * Класс расширяет стандартное отражения экрана для добавления возможностей их удаления из системы.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\model\modules\SystemPackages\InstallerScreens
 */
class ReflectionScreen extends sp\ReflectionScreen{
  /**
   * Метод оповещает используемые экраны об удалении зависимого экрана удаляя информацию о нем из их файлов состояния.
   * @param boolean $lazy [optional] Метод оповещения используемых экранов. false - сообщать об ошибке при отсутствии одного из используемых экранов, true - продолжать оповещение при отсутствии используемых экранов.
   * @throws SystemComponentNotFoundException Выбрасывается в случае отсутствия используемого экрана или доступа к его файлу состояния.
   * @throws InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   */
  public function sayUsed($lazy = false){
    InvalidArgumentException::verifyType($lazy, 'b');
    $components = $this->getUsed();
    foreach($components as $component){
      $component = explode(':', $component);
      try{
        (new ReflectionScreen($component))->removeDestitute($component);
      }
      catch(ScreenNotFoundException $e){
        if(!$lazy){
          throw new SystemComponentNotFoundException('Отсутствует используемый экрана [' . $component . '].', 1, $e);
        }
      }
      catch(RuntimeException $e){
        if(!$lazy){
          throw new SystemComponentNotFoundException('Отсутствует доступ к файлу состояния используемого экрана [' . $component . '].', 1, $e);
        }
      }
    }
  }

  /**
   * Метод удаляет информацию об экране из маршрутизатора.
   * @throws NotFoundDataException Выбрасывается в случае отсутствия доступа к конфигурации системы.
   */
  public function removeRouter(){
    try{
      $router = ViewRouter::getInstance();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
    $router->removeScreen($this->getModuleName(), $this->getScreenName());
  }

  /**
   * Метод удаляет каталог данного экрана из системы.
   * @return string Информация о результатах удаления экрана.
   */
  public function uninstall(){
    $result = 'The screen [' . $this->getName() . '] is removed.';
    // Удаление каталога экрана
    ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . $this->getAddress())->delete(); // Выброс исключений не предполагается
    return $result;
  }
}
