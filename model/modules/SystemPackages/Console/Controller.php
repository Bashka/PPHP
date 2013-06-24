<?php
namespace PPHP\model\modules\SystemPackages\Console;

use PPHP\model\classes\ModuleController;
use PPHP\model\modules\SystemPackages\ReflectionModule;
use PPHP\services\database\ConnectionManager;
use PPHP\services\database\identification\Autoincrement;
use PPHP\services\modules\ModulesRouter;
use PPHP\tools\classes\standard\baseType\exceptions\DuplicationException;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\classes\standard\baseType\exceptions\RuntimeException;
use PPHP\tools\classes\standard\baseType\Integer;
use PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemAddress;
use PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemName;
use PPHP\tools\classes\standard\baseType\special\Name;
use PPHP\tools\classes\standard\fileSystem\ComponentFileSystem;
use PPHP\tools\classes\standard\fileSystem\loadingFiles\LoadedFile;
use PPHP\tools\classes\standard\fileSystem\LockException;
use PPHP\tools\classes\standard\fileSystem\NotExistsException;

/**
 * Модуль предоставляет текстовый интерфейс доступа к установленным модулям системы.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\model\modules\SystemPackages\Console
 */
class Controller extends ModuleController{
  /**
   * Метод предварительно определяет наличие каталога файлов в модуле и создает его в случае необходимости.
   */
  public function afterRun(){
    $dir = ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/SystemPackages/Console/files');
    if(!$dir->isExists()){
      $dir->create();
    }
  }

  /**
   * Метод тестирует механизмы вызова модулей.
   * @param mixed ... Аргументы для тестирования ввода/вывода.
   * @return string
   */
  public function test(){
    $result = 'Reference to the module: passed; ';
    $result .= 'The argument is transferred: ' . implode(', ', func_get_args()) . '.';
    $result .= 'Connection with CSD: ';
    try{
      ConnectionManager::getInstance()->getPDO();
      $result .= 'passed.';
    }
    catch(\Exception $exc){
      $result .= 'failed.';
    }

    return $result;
  }

  /**
   * Метод выполняет синхронизацию постоянного хранилища и используемой системы кэширования.
   * @throws NotFoundDataException Выбрасывается в случае, если не удалось получить доступ к конфигурации системы.
   * @return boolean true - если синхронизация успешно завершена.
   */
  public function synchCahce(){
    try{
      Autoincrement::getInstance()->synch();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }

    return true;
  }

  /**
   * Метод загружает файл в систему.
   * @throws NotExistsException Выбрасывается в случае, если загруженный файл не найден.
   * @throws DuplicationException Выбрасывается в случае, если загруженный файл не может быть перемещен в хранилище консоли из за его переполнения или дублирования.
   * @return string Идентификатор файла.
   */
  public function uploadFile(){
    $newNameFile = (string) rand(0, 999);
    try{
      $file = LoadedFile::getLoadedFile('Filedata');
    }
    catch(NotExistsException $exc){
      throw new NotExistsException('The file is not found', 1, $exc);
    }
    // Выброс дополнительных исключений не предполагается
    try{
      $file->move(ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/SystemPackages/Console/files'));
    }
    catch(DuplicationException $exc){
      return 'The file already exists';
    }
    // Выброс дополнительных исключений не предполагается
    try{
      $file->rename($newNameFile);
    }
    catch(DuplicationException $exc){
      $newNameFile = (string) rand(0, 999);
      // Повторная попытка переименования
      try{
        $file->rename($newNameFile);
      }
      catch(DuplicationException $exc){
        $file->delete();
        throw new DuplicationException('The file already exists', 1, $exc);
      }
    }

    // Выброс дополнительных исключений не предполагается
    return 'The file is loaded. File id - ' . $newNameFile;
  }

  /**
   * Метод возвращает идентификаторы всех файлов, загруженных в систему с помощью метода uploadFile.
   * @return string[] Список идентификаторов файлов.
   */
  public function showFilesID(){
    $dir = ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/SystemPackages/Console/files');
    $result = [];
    foreach($dir->getDirectoryIterator() as $v){
      $fileName = $v->getFilename();
      if($fileName != '.' && $fileName != '..'){
        $result[] = $fileName;
      }
    }
    if(count($result) == 0){
      return 'Files it is not found';
    }

    return $result;
  }

  /**
   * Метод возвращает полный физический адрес к файлу по его идентификатору.
   * @param Integer $fileID Идентификатор файла.
   * @return string Полный физический адрес к файлу.
   */
  public function getAddressFile(Integer $fileID){
    $fileID = $fileID->getVal();
    $file = ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/SystemPackages/Console/files/' . $fileID);
    if(!$file->isExists()){
      return 'The file is not found';
    }

    return $file->getAddress();
  }

  /**
   * Метод удаляет указанный файл из системы.
   * @param Integer $fileID Идентификатор файла.
   * @throws NotExistsException Выбрасывается в случае, если указанного файла не существует.
   * @throws NotExistsException Выбрасывается в случае, если указанный файл на момент вызова метода заблокирован.
   * @return string
   */
  public function removeFile(Integer $fileID){
    $fileID = $fileID->getVal();
    $file = ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/SystemPackages/Console/files/' . $fileID);
    if(!$file->isExists()){
      throw new NotExistsException('The file is not found');
    }
    try{
      $file->delete();
    }
    catch(LockException $exc){
      throw new LockException('The file is blocked', 1, $exc);
    }

    return 'The file is removed';
  }

  /**
   * Метод удаляет все файлы, загруженные с помощью метода uploadFile.
   * @return string
   */
  public function removeAllFiles(){
    ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/SystemPackages/Console/files')->clear();

    return 'Files are removed';
  }

  /**
   * Метод возвращает имена всех зарегистрированных в системе конкретных модулей, упорядоченных в порядке возрастания.
   * @throws NotFoundDataException Выбрасывается в случае, если не удалось получить доступ к конфигурации системы.
   * @return string[] Массив имен зарегистрированных в системе конкретных модулей.
   */
  public function getModulesNames(){
    try{
      $modulesRouter = ModulesRouter::getInstance();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
    $modules = $modulesRouter->getModulesNames();
    foreach($modules as $k => $moduleName){
      $reflectionModule = new ReflectionModule($moduleName);
      if($reflectionModule->getType() != ReflectionModule::SPECIFIC){
        unset($modules[$k]);
      }
    }
    sort($modules);

    return $modules;
  }

  /**
   * Метод определяет доступные для данного модуля методы контроллера.
   * @param Name $module Имя целевого модуля.
   * @throws NotFoundDataException Выбрасывается в случае, если не удалось получить доступ к конфигурации системы или файлу состояния модуля.
   * @throws RuntimeException Выбрасывается в случае, если модуль не является конкретным.
   * @return string[] Массив имен доступных методов контроллера данного модуля.
   */
  public function getModuleActions(Name $module){
    $module = $module->getVal();
    try{
      $router = ModulesRouter::getInstance();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
    try{
      $actions = $router->getModuleActions($module);
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
    catch(RuntimeException $e){
      throw $e;
    }
    if(!$actions){
      return [];
    }
    // Исключение технических методов
    if(($p = array_search('afterRun', $actions)) !== false){
      unset($actions[$p]);
    }
    if(($p = array_search('beforeRun', $actions)) !== false){
      unset($actions[$p]);
    }
    sort($actions);

    return $actions;
  }

  /**
   * Метод возвращает имена аргументов метода контроллера.
   * @param Name $module Целевой модуль.
   * @param Name $method Целевой метод.
   * @throws NotExistsException Выбрасывается в случае, если не удалось получить доступ к конфигурации системы, файлу состояния модуля или заданного метода не существует.
   * @return string[] Массив имен аргументов метода.
   */
  public function getMethodArgs(Name $module, Name $method){
    $module = $module->getVal();
    $method = $method->getVal();
    try{
      $router = ModulesRouter::getInstance();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
    try{
      $controller = (new ReflectionModule($module))->getController();
    }
    catch(NotExistsException $e){
      throw $e;
    }
    $controller = new \ReflectionClass($controller);
    if(!$controller->hasMethod($method)){
      throw new NotFoundDataException('Запрашиваемого метода контроллера не существует.');
    }
    $action = $controller->getMethod($method);
    if(!$action->isPublic() || $action->getDeclaringClass() != $controller){
      throw new NotFoundDataException('Запрашиваемого метода контроллера не существует.');
    }
    $params = $action->getParameters();
    $result = [];
    foreach($params as $param){
      // Определение допустимого типа аргумента
      $classParam = $param->getClass();
      if($classParam){
        $classParam = $classParam->getName();
        $classParam = ':' . substr($classParam, strrpos($classParam, '\\') + 1);
      }
      else{
        $classParam = '';
      }
      $result[] = $param->getName() . $classParam;
    }

    return $result;
  }
}