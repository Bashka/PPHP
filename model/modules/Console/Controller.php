<?php
namespace PPHP\model\modules\Console;

/**
 * Модуль предоставляет текстовый интерфейс доступа к установленным модулям системы.
 */
class Controller extends \PPHP\model\classes\ModuleController{
  /**
   * Метод тестирует механизмы вызова модулей.
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

  public function preInstall(){
    chmod($_SERVER['DOCUMENT_ROOT'].'/PPHP/model/modules', 0777);
    chmod($_SERVER['DOCUMENT_ROOT'].'/PPHP/model/modules/InstallerModules/temp', 0777);
    chmod($_SERVER['DOCUMENT_ROOT'].'/PPHP/model/modules/Console/files', 0777);
    chmod($_SERVER['DOCUMENT_ROOT'].'/PPHP/services/configuration/conf.ini', 0777);
    chmod($_SERVER['DOCUMENT_ROOT'].'/PPHP/tools/classes/standard/fileSystem/loadingFiles/temp', 0777);
    chmod($_SERVER['DOCUMENT_ROOT'].'/PPHP/view/screens', 0777);
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
   * @param integer $fileID Идентификатор файла.
   * @return string Полный физический адрес к файлу.
   */
  public function getAddressFile($fileID){
    $fileID = (int)$fileID;
    $file = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/Console/files/'.$fileID);
    if(!$file->isExists()){
      return 'The file is not found';
    }
    return $file->getAddress();
  }

  /**
   * Метод удаляет указанный файл из системы.
   * @param integer $fileID Идентификатор файла.
   * @return string
   */
  public function removeFile($fileID){
    $fileID = (int)$fileID;
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
   * @param integer $fileID Идентификатор файла.
   * @param string $newName Новое имя файла.
   * @return string
   */
  public function renameFile($fileID, $newName){
    $fileID = (int)$fileID;
    $file = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/Console/files/'.$fileID);
    if(!$file->isExists()){
      return 'The file is not found';
    }
    $file->rename($newName);
    return 'The file is renamed';
  }

  /**
   * Метод перемещает указанный файл.
   * @param integer $fileID Идентификатор перемещаемого файла.
   * @param string $newAddress Новый адрес файла.
   * @return string
   */
  public function moveFile($fileID, $newAddress){
    $fileID = (int)$fileID;
    $file = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/model/modules/Console/files/'.$fileID);
    if(!$file->isExists()){
      return 'The file is not found';
    }
    $file->move(\PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructDirFromAddress($newAddress));
    return 'The file is moved';
  }

  /**
   * Метод возвращает имена всех зарегистрированных в системе модулей.
   * @return array Массив имен зарегистрированных в системе модулей.
   */
  public function getModulesNames(){
    return \PPHP\services\modules\ModulesRouter::getInstance()->getModulesNames();
  }

  /**
   * Метод определяет доступные для данного модуля методы контроллера.
   * @param string $module Имя целевого модуля.
   * @return array Массив доступных методов контроллера данного модуля.
   */
  public function getModuleActions($module){
    $actions = \PPHP\services\modules\ModulesRouter::getInstance()->getModuleActions($module);
    if(!$actions){
      return [];
    }
    return $actions;
  }

  /**
   * Метод возвращает имена аргументов метода контроллера.
   * @param string $module Целевой модуль.
   * @param string $method Целевой метод.
   * @return array Массив имен аргументов метода.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException Выбрасывается в случае, если заданного метода не существует.
   */
  public function getMethodArgs($module, $method){
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
      $result[] = $param->getName();
    }
    return $result;
  }
}