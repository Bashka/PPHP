<?php
namespace PPHP\model\modules\Console;

use PPHP\model\classes\ModuleController;
use PPHP\services\database\ConnectionManager;
use PPHP\services\database\identification\Autoincrement;
use PPHP\services\modules\ModulesRouter;
use PPHP\tools\classes\standard\baseType\exceptions\DuplicationException;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\classes\standard\baseType\Integer;
use PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemAddress;
use PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemName;
use PPHP\tools\classes\standard\baseType\special\Name;
use PPHP\tools\classes\standard\fileSystem\ComponentFileSystem;
use PPHP\tools\classes\standard\fileSystem\loadingFiles\LoadedFile;
use PPHP\tools\classes\standard\fileSystem\LockException;
use PPHP\tools\classes\standard\fileSystem\NotExistsException;
use PPHP\tools\patterns\metadata\reflection\ReflectionModule;

/**
 * Модуль предоставляет текстовый интерфейс доступа к установленным модулям системы.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\model\modules\Console
 */
class Controller extends ModuleController{
  /**
   * Метод тестирует механизмы вызова модулей.
   * @param mixed ... Аргументы для тестирования ввода/вывода.
   * @return string
   */
  public function test(){
    $result = 'Reference to the module: passed; ';
    $result .= 'The argument is transferred: '.implode(', ', func_get_args()).'.';
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
   * @return boolean true - если синхронизация успешно завершена.
   */
  public function synchCahce(){
    Autoincrement::getInstance()->synch();
    return true;
  }

  /**
   * Метод загружает файл в систему.
   * @return string Идентификатор файла.
   */
  public function uploadFile(){
    $newNameFile = (string)rand(0, 99999);
    try{
      $file = LoadedFile::getLoadedFile('Filedata');
    }
    catch(NotExistsException $exc){
      return 'The file is not found';
    }
    try{
      $file->move(ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/Console/files'));
    }
    catch(DuplicationException $exc){
      return 'The file already exists';
    }
    $file->rename($newNameFile);
    return 'The file is loaded. File id - '.$newNameFile;
  }

  /**
   * Метод возвращает идентификаторы всех файлов, загруженных в систему с помощью метода uploadFile.
   * @return string Список идентификаторов файлов.
   */
  public function showFilesID(){
    $dir = ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/Console/files');
    $result = [];
    foreach($dir->getDirectoryIterator() as $v){
      $result[] = $v->getFilename();
    }
    array_shift($result);
    array_shift($result);
    if(count($result) == 0){
      return 'Files it is not found';
    }
    return implode(', ', $result);
  }

  /**
   * Метод возвращает полный физический адрес к файлу по его идентификатору.
   * @param Integer $fileID Идентификатор файла.
   * @return string Полный физический адрес к файлу.
   */
  public function getAddressFile(Integer $fileID){
    $fileID = $fileID->getVal();
    $file = ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/Console/files/'.$fileID);
    if(!$file->isExists()){
      return 'The file is not found';
    }
    return $file->getAddress();
  }

  /**
   * Метод удаляет указанный файл из системы.
   * @param Integer $fileID Идентификатор файла.
   * @return string
   */
  public function removeFile(Integer $fileID){
    $fileID = $fileID->getVal();
    $file = ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/Console/files/'.$fileID);
    if(!$file->isExists()){
      return 'The file is not found';
    }
    try{
      $file->delete();
    }
    catch(LockException $exc){
      return 'The file is blocked';
    }
    return 'The file is removed';
  }

  /**
   * Метод удаляет все файлы, загруженные с помощью метода uploadFile.
   * @return string
   */
  public function removeAllFiles(){
    ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/Console/files')->clear();
    return 'Files are removed';
  }

  /**
   * Метод переименовывает указанный файл.
   * @param Integer $fileID Идентификатор файла.
   * @param FileSystemName $newName Новое имя файла.
   * @return string
   */
  public function renameFile(Integer $fileID, FileSystemName $newName){
    $fileID = $fileID->getVal();
    $newName = $newName->getVal();
    $file = ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/Console/files/'.$fileID);
    if(!$file->isExists()){
      return 'The file is not found';
    }
    $file->rename($newName);
    return 'The file is renamed';
  }

  /**
   * Метод перемещает указанный файл.
   * @param FileSystemName $fileName Имя перемещаемого файла относительно хранилища файлов данного модуля.
   * @param FileSystemAddress $newAddress Новый адрес файла относительно корневого каталога сайта.
   * @return string
   */
  public function moveFile(FileSystemName $fileName, FileSystemAddress $newAddress){
    $fileName = $fileName->getVal();
    $newAddress = ($newAddress->isRoot())? $newAddress->getVal(): '/'.$newAddress->getVal();
    $file = ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/Console/files/'.$fileName);
    if(!$file->isExists()){
      return 'The file is not found';
    }
    $file->move(ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . $newAddress));
    return 'The file is moved';
  }

  /**
   * Метод возвращает имена всех зарегистрированных в системе конкретных модулей, упорядоченных в порядке возрастания.
   * @return array Массив имен зарегистрированных в системе конкретных модулей.
   */
  public function getModulesNames(){
    $modulesRouter = ModulesRouter::getInstance();
    $modules = $modulesRouter->getModulesNames();
    foreach($modules as $k => $moduleName){
      if($modulesRouter->getReflectionModule($moduleName)->getType() != ReflectionModule::SPECIFIC){
        unset($modules[$k]);
      }
    }
    sort($modules);
    return $modules;
  }

  /**
   * Метод определяет доступные для данного модуля методы контроллера.
   * @param Name $module Имя целевого модуля.
   * @return array Массив доступных методов контроллера данного модуля.
   */
  public function getModuleActions(Name $module){
    $module = $module->getVal();
    $actions = ModulesRouter::getInstance()->getModuleActions($module);
    if(!$actions){
      return [];
    }
    sort($actions);
    return $actions;
  }

  /**
   * Метод возвращает имена аргументов метода контроллера.
   * @param Name $module Целевой модуль.
   * @param Name $method Целевой метод.
   * @return array Массив имен аргументов метода.
   * @throws NotFoundDataException Выбрасывается в случае, если заданного метода не существует.
   */
  public function getMethodArgs(Name $module, Name $method){
    $module = $module->getVal();
    $method = $method->getVal();
    $controller = ModulesRouter::getInstance()->getController($module);
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
        $classParam = ':'.substr($classParam, strrpos($classParam, '\\')+1);
      }
      else{
        $classParam = '';
      }
      $result[] = $param->getName().$classParam;
    }
    return $result;
  }
}