<?php
namespace PPHP\model\modules\SystemPackages\InstallerModules;

use PPHP\model\classes\ModuleController;
use PPHP\model\modules\SystemPackages as sp;
use PPHP\services\modules\ModuleDuplicationException;
use PPHP\services\modules\ModuleNotFoundException;
use PPHP\services\modules\ModulesRouter;
use PPHP\tools\classes\standard\baseType\exceptions\DuplicationException;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\classes\standard\baseType\exceptions\RuntimeException;
use PPHP\tools\classes\standard\baseType\exceptions\StructureException;
use PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemAddress;
use PPHP\tools\classes\standard\baseType\special\Name;
use PPHP\tools\classes\standard\baseType\special\network\URL;
use PPHP\tools\classes\standard\fileSystem\NotExistsException;

/**
 * Модуль позволяет добавлять и удалять модули в системе.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\model\modules\SystemPackages\InstallerModules
 */
class Controller extends ModuleController{
  /**
   * Метод возвращает имена всех зарегистрированных в системе модулей.
   * @throws NotFoundDataException Выбрасывается в случае, если не удалось получить доступ к конфигурации системы.
   * @return string[] Массив имен зарегистрированных в системе модулей.
   */
  public function getNamesModules(){
    try{
      $router = ModulesRouter::getInstance();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }

    return $router->getModulesNames();
  }

  /**
   * Метод возвращает массив используемых данным модулем модулей.
   * @param Name $module Имя целевого модуля.
   * @throws ModuleNotFoundException Выбрасывается в случае отсутствия модуля, его файла состояния или каталога.
   * @throws RuntimeException Выбрасывается в случае, если вызываемый модуль не является конкретным.
   * @return string[] Массив имен используемых модулей.
   */
  public function getUsed(Name $module){
    try{
      $module = new sp\ReflectionModule($module->getVal());
    }
    catch(ModuleNotFoundException $e){
      throw $e;
    }
    try{
      return $module->getUsed();
    }
    catch(RuntimeException $e){
      throw $e;
    }
  }

  /**
   * Метод возвращает имя родительского модуля.
   * @param Name $module Имя целевого модуля.
   * @throws ModuleNotFoundException Выбрасывается в случае отсутствия модуля, его файла состояния или каталога.
   * @return string Имя родительского модуля или false, если модуль не имеет родителя.
   */
  public function getParent(Name $module){
    try{
      $module = new sp\ReflectionModule($module->getVal());
    }
    catch(ModuleNotFoundException $e){
      throw $e;
    }

    return $module->getParent();
  }

  /**
   * Метод возвращает массив имен дочерних модулей.
   * @param Name $module Имя целевого модуля.
   * @throws ModuleNotFoundException Выбрасывается в случае отсутствия модуля, его файла состояния или каталога.
   * @return string[] Массив имен дочерних модулей.
   */
  public function getChild(Name $module){
    try{
      $module = new sp\ReflectionModule($module->getVal());
    }
    catch(ModuleNotFoundException $e){
      throw $e;
    }

    return $module->getChild();
  }

  /**
   * Метод возвращает массив имен модулей, зависимых от данного.
   * @param Name $module Имя целевого модуля.
   * @throws ModuleNotFoundException Выбрасывается в случае отсутствия модуля, его файла состояния или каталога.
   * @throws RuntimeException Выбрасывается в случае, если вызываемый модуль не является конкретным.
   * @return string[] Массив имен зависимых модулей.
   */
  public function getDestitute(Name $module){
    try{
      $module = new sp\ReflectionModule($module->getVal());
    }
    catch(ModuleNotFoundException $e){
      throw $e;
    }
    try{
      return $module->getDestitute();
    }
    catch(RuntimeException $e){
      throw $e;
    }
  }

  /**
   * Метод возвращает тип вызываемого модуля.
   * @param Name $module Имя целевого модуля.
   * @throws StructureException Выбрасывается в случает отсутствия обязательного свойства модуля.
   * @throws ModuleNotFoundException Выбрасывается в случае отсутствия модуля, его файла состояния или каталога.
   * @return string Тип модуля.
   */
  public function getType(Name $module){
    try{
      $module = new sp\ReflectionModule($module->getVal());
    }
    catch(ModuleNotFoundException $e){
      throw $e;
    }
    try{
      return $module->getType();
    }
    catch(StructureException $e){
      throw $e;
    }
  }

  /**
   * Метод возвращает версию вызываемого модуля.
   * @param Name $module Имя целевого модуля.
   * @throws StructureException Выбрасывается в случает отсутствия обязательного свойства модуля.
   * @throws ModuleNotFoundException Выбрасывается в случае отсутствия модуля, его файла состояния или каталога.
   * @return string Версия модуля.
   */
  public function getVersion(Name $module){
    try{
      $module = new sp\ReflectionModule($module->getVal());
    }
    catch(ModuleNotFoundException $e){
      throw $e;
    }
    try{
      return $module->getVersion();
    }
    catch(StructureException $e){
      throw $e;
    }
  }

  /**
   * Метод устанавливает указанный локальный модуль.
   * @param FileSystemAddress $archiveAddress Полный адрес архива модуля относительно корневого каталога системы.
   * @throws NotExistsException Выбрасывается в случае, если целевой архив модуля не найден по указанному адресу.
   * @throws StructureException Выбрасывается в случае, если целевой архив модуля имеет недопустимую структуру.
   * @throws ModuleDuplicationException Выбрасывается в случае, если целевой архив модуля уже установлен в системе.
   * @throws ModuleNotFoundException Выбрасывается в случае, если отсутствует необходимый родительский или один из используемых модулей.
   * @throws NotFoundDataException Выбрасывается в случае отсутствия доступа к конфигурации системы.
   * @throws DuplicationException Выбрасывается в случае наличия каталога модуля в системе.
   * @return string Результаты выполнения установки.
   */
  public function installModule(FileSystemAddress $archiveAddress){
    $archiveAddress = $_SERVER['DOCUMENT_ROOT'] . (($archiveAddress->isRoot())? $archiveAddress->getVal() : '/' . $archiveAddress->getVal());
    try{
      $archive = new ReflectionArchiveModule($archiveAddress);
    }
    catch(NotExistsException $e){
      throw $e;
    }
    catch(StructureException $e){
      throw $e;
    }
    if($archive->isDuplication()){
      throw new ModuleDuplicationException('Целевой модуль [' . $archive->getName() . '] уже установлен в системе.');
    }
    if(!$archive->isParent()){
      throw new ModuleNotFoundException('Отсутствует родительский модуль [' . $archive->getParent() . '].');
    }
    if(($use = $archive->isUsed()) !== true){
      throw new ModuleNotFoundException('Отсутствует используемый модуль [' . $use . '].');
    }
    try{
      $archive->addRouter();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
    // Дальнейший выброс исключний не предполагается.
    $archive->sayParent();
    $archive->sayUsed();
    try{
      $result = $archive->install();
    }
    catch(DuplicationException $e){
      throw $e;
    }
    if(ModulesRouter::getInstance()->hasModule('Access')){
      $archive->addAccess();
    }

    return $result;
  }

  /**
   * Метод устанавливает указанный удаленный модуль.
   * @param URL $urlModule URL адрес устанавливаемого архива модуля.
   * @throws NotExistsException Выбрасывается в случае, если целевой архив модуля не найден по указанному адресу.
   * @throws StructureException Выбрасывается в случае, если целевой архив модуля имеет недопустимую структуру.
   * @throws ModuleDuplicationException Выбрасывается в случае, если целевой архив модуля уже установлен в системе.
   * @throws ModuleNotFoundException Выбрасывается в случае, если отсутствует необходимый родительский или один из используемых модулей.
   * @throws NotFoundDataException Выбрасывается в случае отсутствия доступа к конфигурации системы.
   * @return string Результаты выполнения установки.
   */
  public function installModuleURL(URL $urlModule){
    $urlModule = $urlModule->getVal();
    $address = $_SERVER['DOCUMENT_ROOT'] . '/' . ModulesRouter::MODULES_DIR . '/SystemPackages/InstallerModules/0';
    $arch = fopen($address, 'w+');
    fwrite($arch, file_get_contents($urlModule));
    fclose($arch);
    if(!file_exists($address)){
      throw new NotExistsException('Невозможно загрузить архив модуля [' . $urlModule . '].');
    }
    try{
      return $this->installModule(new FileSystemAddress($address));
    }
    catch(NotExistsException $e){
      throw $e;
    }
    catch(StructureException $e){
      throw $e;
    }
    catch(ModuleDuplicationException $e){
      throw $e;
    }
    catch(ModuleNotFoundException $e){
      throw $e;
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
  }

  /**
   * Метод удаляет указанный модуль.
   * @param Name $moduleName Имя удаляемого модуля.
   * @throws ModuleNotFoundException Выбрасывается в случае отсутствия целевого модуля, его родительского модуля или одного из используемых модулей.
   * @throws NotFoundDataException Выбрасывается в случае отсутствия доступа к конфигурации системы.
   * @return string Результаты работы метода.
   */
  public function uninstallModule(Name $moduleName){
    try{
      $module = new ReflectionModule($moduleName->getVal());
    }
    catch(ModuleNotFoundException $e){
      throw $e;
    }
    try{
      $module->sayParent();
      $module->sayUsed();
    }
    catch(ModuleNotFoundException $e){
      throw $e;
    }
    if(ModulesRouter::getInstance()->hasModule('Access')){
      $module->removeAccess(); // Выброс исключений не предполагается
    }
    $result = $module->uninstall();
    try{
      $module->removeRouter();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }

    return $result;
  }
}
