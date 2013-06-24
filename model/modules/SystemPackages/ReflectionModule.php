<?php
namespace PPHP\model\modules\SystemPackages;

use PPHP\model\classes\ModuleController;
use PPHP\services\modules\ModuleNotFoundException;
use PPHP\services\modules\ModulesRouter;
use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\classes\standard\baseType\exceptions\RuntimeException;
use PPHP\tools\classes\standard\baseType\exceptions\StructureException;
use PPHP\tools\classes\standard\fileSystem\ComponentFileSystem;
use PPHP\tools\classes\standard\fileSystem\FileINI;
use PPHP\tools\classes\standard\fileSystem\LockException;
use PPHP\tools\classes\standard\fileSystem\NotExistsException;
use PPHP\tools\patterns\io\IOException;
use PPHP\tools\patterns\metadata as metadata;

/**
 * Отражение модуля.
 * Данный класс является отражением модуля системы с устойчивым состоянием и возможностью аннотирования.
 * Класс может быть инстанциирован только для установленных в системе модулей.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\model\modules\SystemPackages
 */
class ReflectionModule implements metadata\Described{
  use metadata\TDescribed;

  /**
   * Тип модуля - конкретный.
   */
  const SPECIFIC = 'specific';

  /**
   * Тип модуля - виртуальный.
   */
  const VIRTUAL = 'virtual';

  /**
   * Файл состояния модуля.
   * @var FileINI
   */
  protected $fileINI;

  /**
   * Имя модуля.
   * @var string
   */
  protected $moduleName;

  /**
   * Полный физический адрес модуля относительно корня системы.
   * @var string
   */
  protected $moduleAddress;

  /**
   * @param string $moduleName Имя модуля.
   * @throws InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws ModuleNotFoundException Выбрасывается в случае отсутствия модуля, его файла состояния или каталога.
   */
  public function __construct($moduleName){
    InvalidArgumentException::verifyType($moduleName, 'S');
    $this->moduleName = $moduleName;
    try{
      $modulesRouter = ModulesRouter::getInstance();
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
    if(!$modulesRouter->hasModule($moduleName)){
      throw ModuleNotFoundException::getException($moduleName);
    }
    $this->moduleAddress = '/' . $modulesRouter::MODULES_DIR . '/' . $modulesRouter->getModule($moduleName);
    $iniFile = ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . $this->moduleAddress . '/state.ini');
    try{
      if(!$iniFile->isExists()){
        throw new ModuleNotFoundException('Требуемый файл состояния модуля [' . $moduleName . '] не найден.');
      }
    }
    catch(NotExistsException $e){
      throw new ModuleNotFoundException('Каталог модуля [' . $moduleName . '] не найден.', 1, $e);
    }
    $this->fileINI = new FileINI($iniFile, true);
  }

  /**
   * Метод возвращает контроллер данного модуля, если он является конкретным.
   * @throws RuntimeException Выбрасывается в случае, если модуль не явлется конкретным.
   * @return ModuleController Контроллер модуля.
   */
  public function getController(){
    if($this->fileINI->Module_type == self::VIRTUAL){
      throw new RuntimeException('Виртуальный модуль не может использовать контроллер.');
    }
    $controller = str_replace('/', '\\', $this->moduleAddress) . '\\Controller';

    return $controller::getInstance();
  }

  /**
   * Метод возвращает объект класса инсталлятора модуля.
   * @return boolean|\PPHP\model\classes\Installer Инсталлятор модуля или false если модуль не имеет его.
   */
  public function getInstaller(){
    if(file_exists($_SERVER['DOCUMENT_ROOT'] . $this->moduleAddress . '/Installer.php')){
      $installer = str_replace('/', '\\', $this->moduleAddress) . '\\Installer';

      return $installer::getInstance();
    }
    else{
      return false;
    }
  }

  /**
   * @return string
   */
  public function getName(){
    return $this->moduleName;
  }

  /**
   * @return string
   */
  public function getAddress(){
    return $this->moduleAddress;
  }

  /**
   * Метод возвращает версию вызываемого модуля.
   * @throws StructureException Выбрасывается в случает отсутствия обязательного свойства модуля.
   * @return string Версия модуля.
   */
  public function getVersion(){
    $property = $this->fileINI->Module_version;
    if(is_null($property)){
      throw new StructureException('Недопустимая структура файла состояния модуля. Отсутствие обязательного свойства [Module::version].');
    }

    return $property;
  }

  /**
   * Метод возвращает тип вызываемого модуля.
   * @throws StructureException Выбрасывается в случает отсутствия обязательного свойства модуля.
   * @return string Тип модуля.
   */
  public function getType(){
    $property = $this->fileINI->Module_type;
    if(is_null($property)){
      throw new StructureException('Недопустимая структура файла состояния модуля. Отсутствие обязательного свойства [Module::version].');
    }

    return $property;
  }

  /**
   * Метод возвращает имя родительского модуля.
   * @return string Имя родительского модуля или false, если модуль не имеет родителя.
   */
  public function getParent(){
    $property = $this->fileINI->Depending_parent;
    return (!is_null($property))? $property : false;
  }

  /**
   * Метод возвращает массив имен дочерних модулей.
   * @return string[] Массив имен дочерних модулей.
   */
  public function getChild(){
    $modules = trim((string) $this->fileINI->Depending_children);
    if($modules == ''){
      $modules = [];
    }
    else{
      $modules = explode(',', $modules);
    }

    return $modules;
  }

  /**
   * Метод добавляет родительскую связь с дочерним модулем.
   * @param string $moduleName Добавляемое имя дочернего модуля.
   * @throws InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws RuntimeException Выбрасывается в случае блокировки, отсутствия файла состояния модуля или ошибки при работе с потоком вывода.
   * @return boolean true - если связь успешно добавлена, иначе - false.
   */
  public function addChild($moduleName){
    InvalidArgumentException::verifyType($moduleName, 'S');
    $child = $this->getChild();
    if(array_search($moduleName, $child) === false){
      $child[] = $moduleName;
      $this->fileINI->Depending_children = implode(',', $child);
      try{
        $this->fileINI->rewrite();
      }
      catch(LockException $e){
        throw new RuntimeException('Отсутствует доступ к файлу состояния модуля.', 1, $e);
      }
      catch(NotExistsException $e){
        throw new RuntimeException('Отсутствует доступ к файлу состояния модуля.', 1, $e);
      }
      catch(IOException $e){
        throw new RuntimeException('Отсутствует доступ к файлу состояния модуля.', 1, $e);
      }

      return true;
    }

    return false;
  }

  /**
   * Метод удаляет родительскую связь с дочерним модулем.
   * @param string $moduleName Удаляемое имя дочернего модуля.
   * @throws InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws RuntimeException Выбрасывается в случае блокировки, отсутствия файла состояния модуля или ошибки при работе с потоком вывода.
   * @return boolean true - если связь успешно удалена, иначе - false.
   */
  public function removeChild($moduleName){
    InvalidArgumentException::verifyType($moduleName, 'S');
    $child = $this->getChild();
    if(($key = array_search($moduleName, $child)) !== false){
      unset($child[$key]);
      $this->fileINI->Depending_children = implode(',', $child);
      try{
        $this->fileINI->rewrite();
      }
      catch(LockException $e){
        throw new RuntimeException('Отсутствует доступ к файлу состояния модуля.', 1, $e);
      }
      catch(NotExistsException $e){
        throw new RuntimeException('Отсутствует доступ к файлу состояния модуля.', 1, $e);
      }
      catch(IOException $e){
        throw new RuntimeException('Отсутствует доступ к файлу состояния модуля.', 1, $e);
      }

      return true;
    }

    return false;
  }

  /**
   * Метод возвращает массив используемых данным модулем модулей.
   * @throws RuntimeException Выбрасывается в случае, если вызываемый модуль не является конкретным.
   * @return string[]|boolean Массив имен используемых модулей или false, если модуль не имеет зависимостей.
   */
  public function getUsed(){
    if($this->getVersion() === self::VIRTUAL){
      throw new RuntimeException('Модуль [' . $this->getName() . '] не является конкретным и не может использовать другие модули.');
    }
    $modules = trim((string) $this->fileINI->Depending_used);
    if($modules == ''){
      $modules = false;
    }
    else{
      $modules = explode(',', $modules);
    }

    return $modules;
  }

  /**
   * Метод возвращает массив имен модулей, зависимых от данного.
   * @throws RuntimeException Выбрасывается в случае, если вызываемый модуль не является конкретным.
   * @return string[] Массив имен зависимых модулей.
   */
  public function getDestitute(){
    if($this->getVersion() === self::VIRTUAL){
      throw new RuntimeException('Модуль [' . $this->getName() . '] не является конкретным и не может быть использовать другими модулями.');
    }
    $modules = trim((string) $this->fileINI->Depending_destitute);
    if($modules == ''){
      $modules = [];
    }
    else{
      $modules = explode(',', $modules);
    }

    return $modules;
  }

  /**
   * Метод добавляет зависимость от текущего модуля.
   * @param string $moduleName Добавляемое имя зависимого модуля.
   * @throws InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws RuntimeException Выбрасывается в случае блокировки, отсутствия файла состояния модуля или ошибки при работе с потоком вывода.
   * @return boolean true - если связь успешно добавлена, иначе - false.
   */
  public function addDestitute($moduleName){
    InvalidArgumentException::verifyType($moduleName, 'S');
    $destitute = $this->getDestitute();
    if(array_search($moduleName, $destitute) === false){
      $destitute[] = $moduleName;
      $this->fileINI->Depending_destitute = implode(',', $destitute);
      try{
        $this->fileINI->rewrite();
      }
      catch(LockException $e){
        throw new RuntimeException('Отсутствует доступ к файлу состояния модуля.', 1, $e);
      }
      catch(NotExistsException $e){
        throw new RuntimeException('Отсутствует доступ к файлу состояния модуля.', 1, $e);
      }
      catch(IOException $e){
        throw new RuntimeException('Отсутствует доступ к файлу состояния модуля.', 1, $e);
      }

      return true;
    }

    return false;
  }

  /**
   * Метод удаляет зависимость от текущего модуля.
   * @param string $moduleName Удаляемое имя зависимого модуля.
   * @throws InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws RuntimeException Выбрасывается в случае блокировки, отсутствия файла состояния модуля или ошибки при работе с потоком вывода.
   * @return boolean true - если связь успешно удалена, иначе - false.
   */
  public function removeDestitute($moduleName){
    InvalidArgumentException::verifyType($moduleName, 'S');
    $destitute = $this->getDestitute();
    if(($key = array_search($moduleName, $destitute)) !== false){
      unset($destitute[$key]);
      $this->fileINI->Depending_destitute = implode(',', $destitute);
      try{
        $this->fileINI->rewrite();
      }
      catch(LockException $e){
        throw new RuntimeException('Отсутствует доступ к файлу состояния модуля.', 1, $e);
      }
      catch(NotExistsException $e){
        throw new RuntimeException('Отсутствует доступ к файлу состояния модуля.', 1, $e);
      }
      catch(IOException $e){
        throw new RuntimeException('Отсутствует доступ к файлу состояния модуля.', 1, $e);
      }

      return true;
    }

    return false;
  }

  /**
   * @return array Ассоциативный массив имен методов контроллера модуля и запрещающих их ролей, имеющий следующую структуру: [имяМетода => [роль, ...], ...].
   */
  public function getAccess(){
    if($this->getVersion() === self::VIRTUAL){
      throw new RuntimeException('Модуль [' . $this->getName() . '] не является конкретным и не может иметь правила доступа.');
    }
    if($this->fileINI->isSectionExists('Access')){
      $accesses = $this->fileINI->getSection('Access');
      foreach($accesses as &$access){
        $access = explode(',', $access);
      }
    }
    else{
      $accesses = [];
    }

    return $accesses;
  }
}