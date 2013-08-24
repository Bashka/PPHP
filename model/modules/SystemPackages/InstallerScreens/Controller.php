<?php
namespace PPHP\model\modules\SystemPackages\InstallerScreens;

use PPHP\model\classes\ModuleController;
use PPHP\model\modules\SystemPackages\SystemComponentNotFoundException;
use PPHP\services\modules\ModulesRouter;
use PPHP\services\view\ScreenDuplicationException;
use PPHP\services\view\ScreenNotFoundException;
use PPHP\services\view\ViewRouter;
use PPHP\tools\classes\standard\baseType\exceptions\DuplicationException;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\classes\standard\baseType\exceptions\StructureException;
use PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemAddress;
use PPHP\tools\classes\standard\baseType\special\Name;
use PPHP\tools\classes\standard\baseType\special\network\URL;
use PPHP\tools\classes\standard\fileSystem\NotExistsException;

/**
 * Модуль позволяет добавлять и удалять экраны в системе.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\model\modules\SystemPackages\InstallerScreens
 */
class Controller extends ModuleController{
  /**
   * Метод возвращает имена всех зарегистрированных в системе экранов.
   * @throws NotFoundDataException Выбрасывается в случае, если не удалось получить доступ к конфигурации системы.
   * @return string[] Массив имен зарегистрированных в системе экранов.
   */
  public function getNamesScreens(){
    try{
      $router = ViewRouter::getInstance();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }

    return $router->getScreensNames();
  }

  /**
   * Метод возвращает массив используемых данным экраном экранов.
   * @param Name $module Имя модуля экрана.
   * @param Name $screen Имя целевого экрана.
   * @throws SystemComponentNotFoundException Выбрасывается в случае отсутствия экрана, его файла состояния или каталога.
   * @return string[] Массив имен используемых экранов.
   */
  public function getUsed(Name $module, Name $screen){
    try{
      $screen = new ReflectionScreen($module->getVal() . ':' . $screen->getVal());
    }
    catch(SystemComponentNotFoundException $e){
      throw $e;
    }

    return $screen->getUsed();
  }

  /**
   * Метод возвращает массив имен экранов, зависимых от данного.
   * @param Name $module Имя модуля экрана.
   * @param Name $screen Имя целевого экрана.
   * @throws SystemComponentNotFoundException Выбрасывается в случае отсутствия экрана, его файла состояния или каталога.
   * @return string[] Массив имен зависимых модулей.
   */
  public function getDestitute(Name $module, Name $screen){
    try{
      $screen = new ReflectionScreen($module->getVal() . ':' . $screen->getVal());
    }
    catch(SystemComponentNotFoundException $e){
      throw $e;
    }

    return $screen->getDestitute();
  }

  /**
   * Метод возвращает версию вызываемого экрана.
   * @param Name $module Имя модуля экрана.
   * @param Name $screen Имя целевого экрана.
   * @throws StructureException Выбрасывается в случает отсутствия обязательного свойства экрана.
   * @throws SystemComponentNotFoundException Выбрасывается в случае отсутствия экрана, его файла состояния или каталога.
   * @return string Версия экрана.
   */
  public function getVersion(Name $module, Name $screen){
    try{
      $screen = new ReflectionScreen($module->getVal() . ':' . $screen->getVal());
    }
    catch(SystemComponentNotFoundException $e){
      throw $e;
    }
    try{
      return $screen->getVersion();
    }
    catch(StructureException $e){
      throw $e;
    }
  }

  /**
   * Метод устанавливает указанный локальный экран.
   * @param FileSystemAddress $archiveAddress Полный адрес архива экрана относительно корневого каталога сайта.
   * @return string Результаты выполнения установки.
   */
  public function installScreen(FileSystemAddress $archiveAddress){
    $archiveAddress = $_SERVER['DOCUMENT_ROOT'] . (($archiveAddress->isRoot())? $archiveAddress->getVal() : '/' . $archiveAddress->getVal());
    try{
      $archive = new ReflectionArchiveScreen($archiveAddress);
    }
    catch(NotExistsException $e){
      throw $e;
    }
    catch(StructureException $e){
      throw $e;
    }
    if($archive->isDuplication()){
      throw new ScreenDuplicationException('Целевой экран [' . $archive->getName() . '] уже установлен в системе.');
    }
    if(($use = $archive->isUsed()) !== true){
      throw new ScreenNotFoundException('Отсутствует используемый экран [' . $use . '].');
    }
    try{
      $archive->addRouter();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
    // Дальнейший выброс исключний не предполагается.
    $archive->sayUsed();
    try{
      $result = $archive->install();
    }
    catch(DuplicationException $e){
      throw $e;
    }

    return $result;
  }

  /**
   * Метод устанавливает указанный удаленный экран.
   * @param URL $urlScreen URL устанавливаемого экрана.
   * @return boolean|string false - если экран не удалось установить.
   */
  public function installScreenURL(URL $urlScreen){
    $urlScreen = $urlScreen->getVal();
    $address = $_SERVER['DOCUMENT_ROOT'] . '/' . ModulesRouter::MODULES_DIR . '/SystemPackages/InstallerScreens/0';
    $arch = fopen($address, 'w+');
    fwrite($arch, file_get_contents($urlScreen));
    fclose($arch);
    if(!file_exists($address)){
      throw new NotExistsException('Невозможно загрузить архив экрана [' . $urlScreen . '].');
    }
    try{
      return $this->installScreen(new FileSystemAddress($address));
    }
    catch(NotExistsException $e){
      throw $e;
    }
    catch(StructureException $e){
      throw $e;
    }
    catch(ScreenDuplicationException $e){
      throw $e;
    }
    catch(ScreenNotFoundException $e){
      throw $e;
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
  }

  /**
   * Метод удаляет указанный экран.
   * @param Name $module Имя родительского модуля удаляемого экрана.
   * @param Name $screen Имя удаляемого экрана.
   * @throws SystemComponentNotFoundException Выбрасывается в случае отсутствия используемого экрана или доступа к его файлу состояния.
   * @throws NotFoundDataException Выбрасывается в случае отсутствия доступа к конфигурации системы.
   * @return string Результаты работы метода.
   */
  public function uninstallScreen(Name $module, Name $screen){
    try{
      $screen = new ReflectionScreen($module->getVal() . ':' . $screen->getVal());
    }
    catch(SystemComponentNotFoundException $e){
      throw $e;
    }
    try{
      $screen->sayUsed();
    }
    catch(SystemComponentNotFoundException $e){
      throw $e;
    }
    try{
      $screen->removeRouter();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }

    return $screen->uninstall();
  }
}