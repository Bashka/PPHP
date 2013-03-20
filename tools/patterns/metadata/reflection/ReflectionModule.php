<?php
namespace PPHP\tools\patterns\metadata\reflection;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use \PPHP\tools\patterns\metadata as metadata;
use \PPHP\tools\classes\standard\fileSystem as fileSystem;
use \PPHP\model\classes\Installer as Installer;
use \PPHP\services as services;

/**
 * Отражение модуля.
 *
 * Данный класс является отражением модуля системы с устойчивым состоянием и возможностью аннотирования.
 * Класс может быть инстанциирован только для установленных в системе модулей.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\metadata\reflection
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
  private $ini;

  /**
   * Имя модуля.
   * @var string
   */
  protected $name;
  /**
   * Тип модуля.
   * @var string
   */
  protected $type;
  /**
   * Расположение модуля относительно хранилища модулей.
   * @var string
   */
  protected $location;
  /**
   * Расположение модуля относительно корня системы.
   * @var string
   */
  protected $address;
  /**
   * Версия  модуля.
   * @var string
   */
  protected $version;
  /**
   * Внутренний инсталятор модуля или null - если модуль не имеет внутреннего инсталлятора.
   *
   * @var \PPHP\model\classes\Installer|null
   */
  protected $installer;

  /**
   * Имя родительского модуля или null - если модуль не является дочерним.
   *
   * @var null|string
   */
  protected $parent;
  /**
   * Имена дочерних модулей.
   * @var string[]
   */
  protected $children;

  /**
   * Имена используемых модулей. Null - если модуль виртуальный.
   *
   * @var string[]|null
   */
  protected $used;
  /**
   * Имена зависимых модулей. Null - если модуль виртуальный.
   *
   * @var string[]|null
   */
  protected $destitute;

  /**
   * Ассоциативный массив имен методов контроллера модуля и запрещающих их ролей. Null - если модуль виртуальный.
   *
   * @var array|null
   */
  protected $access;

  /**
   * @param string $moduleName     Имя модуля.
   * @param string $locationModule Расположение каталога модуля относительно хранилаща модулей.
   * @param string $addressModule  Расположение каталога модуля относительно корня системы.
   *
   * @throws fileSystem\NotExistsException Выбрасывается в случае отсутствия файла состояния модуля.
   */
  public function __construct($moduleName, $locationModule, $addressModule){
    // @todo: обработать возможные исключения
    $this->modulesRouter = services\modules\ModulesRouter::getInstance();
    $this->name = $moduleName;
    $this->location = $locationModule;
    $this->address = $addressModule;
    // @todo: обработать возможные исключения
    $iniFile = fileSystem\ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/' . $this->address . '/state.ini');
    // @todo: обработать возможные исключения
    if(!$iniFile->isExists()){
      throw new fileSystem\NotExistsException('Требуемый файл состояния модуля не найден.');
    }
    // @todo: обработать возможные исключения
    $this->ini = new fileSystem\FileINI($iniFile, true);

    // @todo: обработать возможные исключения
    $this->version = $this->ini->get('version', 'Module');
    $this->type = $this->ini->get('type', 'Module');
    $installerAddress = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->address . '/Installer.php';
    if(file_exists($installerAddress)){
      $installerAddress = '\\' . str_replace('/', '\\', $this->address) . '\\Installer';
      $this->installer = $installerAddress::getInstance();
    }
    else{
      $this->installer = null;
    }

    // @todo: обработать возможные исключения
    $this->parent = $this->ini->get('parent', 'Depending');
    if($this->parent == ''){
      $this->parent = null;
    }
    // @todo: обработать возможные исключения
    $this->children = trim((string)$this->ini->get('children', 'Depending'));
    if($this->children == ''){
      $this->children = [];
    }
    else{
      $this->children = explode(',', $this->children);
    }

    if($this->type == self::SPECIFIC){
      $this->used = trim((string)$this->ini->get('used', 'Depending'));
      if($this->used == ''){
        $this->used = [];
      }
      else{
        $this->used = explode(',', $this->used);
      }

      // @todo: обработать возможные исключения
      $this->destitute = trim((string)$this->ini->get('destitute', 'Depending'));
      if($this->destitute == ''){
        $this->destitute = [];
      }
      else{
        $this->destitute = explode(',', $this->destitute);
      }

      // @todo: обработать возможные исключения
      if($this->ini->isSectionExists('Access')){
        $this->access = $this->ini->getSection('Access');
        foreach($this->access as &$access){
          $access = explode(',', $access);
        }
      }
      else{
        $this->access = [];
      }
    }
  }

  /**
   * Метод возвращает контроллер данного модуля, если он является конкретным.
   * @throws exceptions\LogicException Выбрасывается в случае, если модуль не явлется конкретным.
   * @return \PPHP\model\classes\ModuleController Контроллер модуля.
   */
  public function getController(){
    if($this->type == self::VIRTUAL){
      throw new exceptions\LogicException('Виртуальный модуль не может использовать контроллер.');
    }
    $controller = '\\' . str_replace('/', '\\', $this->address) . '\\Controller';
    return $controller::getInstance();
  }

  /**
   * Метод перезаписывает указанное множественное свойство разделяя его элементы запятой.
   *
   * @param string $propertyName Имя свойства.
   * @param string $section      Имя раздела.
   */
  protected function rewriteProperty($propertyName, $section){
    // @todo: обработать возможные исключения
    $this->ini->set($propertyName, implode(',', $this->$propertyName), $section);
    $this->ini->rewrite();
  }

  /**
   * Метод добавляет родительскую связь с дочерним модулем.
   *
   * @param string $childModuleName Имя добавляемого дочернего модуля.
   *
   * @return boolean true - если связь успешно добавлена, иначе - false.
   */
  public function addChild($childModuleName){
    if(($key = array_search($childModuleName, $this->children)) === false){
      $this->children[] = $childModuleName;
      // @todo: обработать возможные исключения
      $this->rewriteProperty('children', 'Depending');
      return true;
    }
    return false;
  }

  /**
   * Метод удаляет родительскую связь с дочерним модулем.
   *
   * @param string $childModuleName Имя удаляемого дочернего модуля.
   *
   * @return boolean true - если связь успешно удалена, иначе - false.
   */
  public function removeChild($childModuleName){
    if(($key = array_search($childModuleName, $this->children)) !== false){
      unset($this->children[$key]);
      // @todo: обработать возможные исключения
      $this->rewriteProperty('children', 'Depending');
      return true;
    }
    return false;
  }

  /**
   * Метод добавляет зависимость от текущего модуля.
   *
   * @param string $moduleName Имя добавляемого зависимого модуля.
   *
   * @return boolean true - если связь успешно добавлена, иначе - false.
   */
  public function addDestitute($moduleName){
    if(($key = array_search($moduleName, $this->destitute)) === false){
      $this->destitute[] = $moduleName;
      // @todo: обработать возможные исключения
      $this->rewriteProperty('destitute', 'Depending');
      return true;
    }
    return false;
  }

  /**
   * Метод удаляет зависимость от текущего модуля.
   *
   * @param string $moduleName Имя уталяемого зависимого модуля.
   *
   * @return boolean true - если связь успешно удалена, иначе - false.
   */
  public function removeDestitute($moduleName){
    if(($key = array_search($moduleName, $this->destitute)) !== false){
      unset($this->destitute[$key]);
      // @todo: обработать возможные исключения
      $this->rewriteProperty('destitute', 'Depending');
      return true;
    }
    return false;
  }

  /**
   * @param string $version
   */
  public function setVersion($version){
    $this->version = $version;
    // @todo: обработать возможные исключения
    $this->ini->set('version', $version, 'Module');
    $this->ini->rewrite();
  }

  /**
   * @return array|null
   */
  public function getAccess(){
    return $this->access;
  }

  /**
   * @return string
   */
  public function getAddress(){
    return $this->address;
  }

  /**
   * @return mixed
   */
  public function getChildren(){
    return $this->children;
  }

  /**
   * @return array|null|\string[]
   */
  public function getDestitute(){
    return $this->destitute;
  }

  /**
   * @return string
   */
  public function getLocation(){
    return $this->location;
  }

  /**
   * @return string
   */
  public function getName(){
    return $this->name;
  }

  /**
   * @return null|string
   */
  public function getParent(){
    return $this->parent;
  }

  /**
   * @return string
   */
  public function getType(){
    return $this->type;
  }

  /**
   * @return array|null|\string[]
   */
  public function getUsed(){
    return $this->used;
  }

  /**
   * @return string
   */
  public function getVersion(){
    return $this->version;
  }

  /**
   * @return null|\PPHP\model\classes\Installer
   */
  public function getInstaller(){
    return $this->installer;
  }
}