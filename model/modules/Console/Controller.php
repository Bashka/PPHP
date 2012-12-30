<?php
namespace PPHP\model\modules\Console;

/**
 * Модуль предоставляет текстовый интерфейс доступа к установленным модулям системы.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\model\modules\Console
 */
class Controller extends \PPHP\model\classes\ModuleController{
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
      \PPHP\services\database\ConnectionManager::getInstance()->getPDO();
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
    \PPHP\services\database\identification\Autoincrement::getInstance()->synch();
    return true;
  }

  /**
   * Метод загружает файл в систему.
   * @return string Идентификатор файла.
   */
  public function uploadFile(){
    $newNameFile = (string)rand(0, 99999);
    try{
      $file = \PPHP\tools\classes\standard\fileSystem\loadingFiles\LoadedFile::getLoadedFile('Filedata');
    }
    catch(\PPHP\tools\classes\standard\fileSystem\NotExistsException $exc){
      return 'The file is not found';
    }
    try{
      $file->move(\PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/Console/files'));
    }
    catch(\PPHP\tools\classes\standard\fileSystem\ComponentDuplicationException $exc){
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
    $dir = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/Console/files');
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
   * @param \PPHP\tools\classes\standard\baseType\Integer $fileID Идентификатор файла.
   * @return string Полный физический адрес к файлу.
   */
  public function getAddressFile(\PPHP\tools\classes\standard\baseType\Integer $fileID){
    $fileID = $fileID->getVal();
    $file = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/Console/files/'.$fileID);
    if(!$file->isExists()){
      return 'The file is not found';
    }
    return $file->getAddress();
  }

  /**
   * Метод удаляет указанный файл из системы.
   * @param \PPHP\tools\classes\standard\baseType\Integer $fileID Идентификатор файла.
   * @return string
   */
  public function removeFile(\PPHP\tools\classes\standard\baseType\Integer $fileID){
    $fileID = $fileID->getVal();
    $file = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/Console/files/'.$fileID);
    if(!$file->isExists()){
      return 'The file is not found';
    }
    try{
      $file->delete();
    }
    catch(\PPHP\tools\classes\standard\fileSystem\LockException $exc){
      return 'The file is blocked';
    }
    return 'The file is removed';
  }

  /**
   * Метод удаляет все файлы, загруженные с помощью метода uploadFile.
   * @return string
   */
  public function removeAllFiles(){
    \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/Console/files')->clear();
    return 'Files are removed';
  }

  /**
   * Метод переименовывает указанный файл.
   * @param \PPHP\tools\classes\standard\baseType\Integer $fileID Идентификатор файла.
   * @param \PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemName $newName Новое имя файла.
   * @return string
   */
  public function renameFile(\PPHP\tools\classes\standard\baseType\Integer $fileID, \PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemName $newName){
    $fileID = $fileID->getVal();
    $newName = $newName->getVal();
    $file = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/Console/files/'.$fileID);
    if(!$file->isExists()){
      return 'The file is not found';
    }
    $file->rename($newName);
    return 'The file is renamed';
  }

  /**
   * Метод перемещает указанный файл.
   * @param \PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemName $fileName Имя перемещаемого файла относительно хранилища файлов данного модуля.
   * @param \PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemAddress $newAddress Новый адрес файла относительно корневого каталога сайта.
   * @return string
   */
  public function moveFile(\PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemName $fileName, \PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemAddress $newAddress){
    $fileName = $fileName->getVal();
    $newAddress = ($newAddress->isRoot())? $newAddress->getVal(): '/'.$newAddress->getVal();
    $file = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/Console/files/'.$fileName);
    if(!$file->isExists()){
      return 'The file is not found';
    }
    $file->move(\PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . $newAddress));
    return 'The file is moved';
  }

  /**
   * Метод возвращает имена всех зарегистрированных в системе конкретных модулей, упорядоченных в порядке возрастания.
   * @return array Массив имен зарегистрированных в системе конкретных модулей.
   */
  public function getModulesNames(){
    $modulesRouter = \PPHP\services\modules\ModulesRouter::getInstance();
    $modules = $modulesRouter->getModulesNames();
    foreach($modules as $k => $moduleName){
      if($modulesRouter->getReflectionModule($moduleName)->getType() != \PPHP\tools\patterns\metadata\reflection\ReflectionModule::SPECIFIC){
        unset($modules[$k]);
      }
    }
    sort($modules);
    return $modules;
  }

  /**
   * Метод определяет доступные для данного модуля методы контроллера.
   * @param \PPHP\tools\classes\standard\baseType\special\Name $module Имя целевого модуля.
   * @return array Массив доступных методов контроллера данного модуля.
   */
  public function getModuleActions(\PPHP\tools\classes\standard\baseType\special\Name $module){
    $module = $module->getVal();
    $actions = \PPHP\services\modules\ModulesRouter::getInstance()->getModuleActions($module);
    if(!$actions){
      return [];
    }
    sort($actions);
    return $actions;
  }

  /**
   * Метод возвращает имена аргументов метода контроллера.
   * @param \PPHP\tools\classes\standard\baseType\special\Name $module Целевой модуль.
   * @param \PPHP\tools\classes\standard\baseType\special\Name $method Целевой метод.
   * @return array Массив имен аргументов метода.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае, если заданного метода не существует.
   */
  public function getMethodArgs(\PPHP\tools\classes\standard\baseType\special\Name $module, \PPHP\tools\classes\standard\baseType\special\Name $method){
    $module = $module->getVal();
    $method = $method->getVal();
    $controller = \PPHP\services\modules\ModulesRouter::getInstance()->getController($module);
    $controller = new \ReflectionClass($controller);
    if(!$controller->hasMethod($method)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Запрашиваемого метода контроллера не существует.');
    }
    $action = $controller->getMethod($method);
    if(!$action->isPublic() || $action->getDeclaringClass() != $controller){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException('Запрашиваемого метода контроллера не существует.');
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