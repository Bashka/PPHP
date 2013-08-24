<?php
namespace PPHP\model\modules\SystemPackages\InstallerScreens;

use PPHP\model\modules\SystemPackages\ReflectionArchive;
use PPHP\model\modules\SystemPackages\SystemComponentNotFoundException;
use PPHP\services\view\ScreenDuplicationException;
use PPHP\services\view\ViewRouter;
use PPHP\tools\classes\standard\baseType\exceptions\DuplicationException;
use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\classes\standard\baseType\exceptions\RuntimeException;
use PPHP\tools\classes\standard\fileSystem\ComponentFileSystem;

/**
 * Отражение архива экрана системы.
 * Класс может быть инстанциирован только для архивов экранов с правильной структурой.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\model\modules\SystemPackages\InstallerScreens
 */
class ReflectionArchiveScreen extends ReflectionArchive{
  /**
   * Метод возвращает имя модуля, которому принадлежит экран.
   * @return string Имя модуля экрана.
   */
  public function getModuleName(){
    $name = explode(':', $this->getName());

    return $name[0];
  }

  /**
   * Метод возвращает имя экрана.
   * @return string Имя экрана.
   */
  public function getScreenName(){
    $name = explode(':', $this->getName());

    return $name[1];
  }

  /**
   * Метод определяет, установлен ли данный экран в системе.
   * @throws NotFoundDataException Выбрасывается в случае отсутствия доступа к конфигурации системы.
   * @return boolean true - если экран с данным именем уже установлен в системе, иначе - false,
   */
  public function isDuplication(){
    try{
      $router = ViewRouter::getInstance();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }

    return $router->hasScreen($this->getModuleName(), $this->getScreenName());
  }

  /**
   * Метод добавляет информацию об экране в роутер.
   * @throws NotFoundDataException Выбрасывается в случае отсутствия доступа к конфигурации системы.
   * @throw ScreenDuplicationException Выбрасывается в случае наличия экрана с данным именем в роутере.
   */
  public function addRouter(){
    try{
      $router = ViewRouter::getInstance();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
    try{
    }
    catch(ScreenDuplicationException $e){
      throw $e;
    }
    $module = $this->getModuleName();
    $screen = $this->getScreenName();
    $router->addScreen($module, $screen, $module . '/' . $screen);
  }

  /**
   * Метод определяет, установлены ли все используемые экраны в системе.
   * @throws NotFoundDataException Выбрасывается в случае отсутствия доступа к конфигурации системы.
   * @return boolean|string true - если все используемые экраны установлены, иначе метод возвращает имя первого неустановленного экрана.
   */
  public function isUsed(){
    try{
      $router = ViewRouter::getInstance();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
    if($this->hasUsed()){
      $used = $this->getUsed();
      foreach($used as $screen){
        $name = explode(':', $screen);
        if(!$router->hasScreen($name[0], $name[1])){
          return $screen;
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
    foreach($used as $screen){
      try{
        (new ReflectionScreen($screen))->addDestitute($name);
      }
      catch(SystemComponentNotFoundException $e){
        if(!$lazy){
          throw new SystemComponentNotFoundException('Отсутствует используемый экран [' . $screen . '].', 1, $e);
        }
      }
      catch(RuntimeException $e){
        if(!$lazy){
          throw new SystemComponentNotFoundException('Отсутствует доступ к файлу состояния используемого экрана [' . $screen . '].', 1, $e);
        }
      }
    }
  }

  /**
   * Метод распаковывает данный архив в систему.
   * @throws DuplicationException Выбрасывается в случае наличия каталога экрана в системе.
   * @return string Информация о результатах установки экрана.
   */
  public function install(){
    $name = $this->getName();
    $result = 'The screen [' . $name . '] is installed.';
    // Распаковка архива
    $address = $_SERVER['DOCUMENT_ROOT'] . '/' . ViewRouter::SCREENS_DIR . '/';
    try{
      $rootDir = ComponentFileSystem::constructDirFromAddress($address . $this->getModuleName() . '/' . $this->getScreenName());
      $rootDir->create();
    }
    catch(DuplicationException $e){
      throw new DuplicationException('Невозможно распаковать архив. Целевой экран [' . $name . '] уже существует в файловой системе.', 1, $e);
    }
    try{
      $this->expand($rootDir);
    }
    catch(DuplicationException $e){
      throw $e;
    }

    return $result;
  }
}