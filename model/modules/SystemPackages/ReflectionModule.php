<?php
namespace PPHP\model\modules\SystemPackages;

use PPHP\model\classes\Installer;
use PPHP\model\classes\ModuleController;
use PPHP\services\modules\ModuleNotFoundException;
use PPHP\services\modules\ModulesRouter;
use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;
use PPHP\tools\classes\standard\baseType\exceptions\RuntimeException;
use PPHP\tools\classes\standard\baseType\exceptions\StructureException;
use PPHP\tools\classes\standard\fileSystem\ComponentFileSystem;
use PPHP\tools\classes\standard\fileSystem\LockException;
use PPHP\tools\classes\standard\fileSystem\NotExistsException;
use PPHP\tools\patterns\io\IOException;
use PPHP\tools\patterns\metadata as metadata;

/**
 * Отражение модуля.
 * Объекты данного класса являются отражениями модулей системы с устойчивым состоянием и возможностью аннотирования.
 * Класс может быть инстанциирован только для установленных в системе модулей.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\model\modules\SystemPackages
 */
class ReflectionModule extends ReflectionSystemComponent{
  /**
   * Тип модуля - конкретный.
   */
  const SPECIFIC = 'specific';

  /**
   * Тип модуля - виртуальный.
   */
  const VIRTUAL = 'virtual';

  /**
   * Расположение модуля относительно хранилища модулей.
   * @var string
   */
  protected $location;

  public function __construct($name){
    try{
      $this->location = ModulesRouter::getInstance()->getModule($name);
    }
    catch(ModuleNotFoundException $e){
      throw new SystemComponentNotFoundException('Требуемый модуль [' . $this->name . '] не найден.', 1, $e);
    }
    catch(NotFoundDataException $e){
      throw $e;
    }
    parent::__construct($name);
  }

  /**
   * Метод возвращает физический адрес каталога компонента от корня системы. Адрес начинается и заканчивается символами косой черты (/).
   * @return string Физический адрес каталога компонента от корня системы.
   */
  public function getAddress(){
    return '/' . ModulesRouter::MODULES_DIR . '/' . $this->location . '/';
  }

  /**
   * Метод возвращает тип вызываемого модуля.
   * @throws StructureException Выбрасывается в случает отсутствия обязательного свойства модуля.
   * @return string Тип модуля.
   */
  public function getType(){
    $property = $this->ini->Component_type;
    if(is_null($property)){
      throw new StructureException('Недопустимая структура файла состояния модуля. Отсутствие обязательного свойства [Component::version].');
    }

    return $property;
  }

  /**
   * Метод возвращает массив имен компонентов, зависимых от данного.
   * @throws RuntimeException Выбрасывается в случае вызова метода для виртуального модуля.
   * @return string[] Массив имен зависимых компонентов.
   */
  public function getDestitute(){
    if($this->getType() == self::VIRTUAL){
      throw new RuntimeException('Вируальный модуль не может иметь зависимости.');
    }
    else{
      return parent::getDestitute();
    }
  }

  /**
   * Метод добавляет зависимость от текущего компонента.
   * @param string $name Добавляемое имя зависимого компонента.
   * @throws InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws RuntimeException Выбрасывается в случае блокировки, отсутствия файла состояния компонента, ошибки при работе с потоком вывода или вызове метода для виртуального модуля.
   * @return boolean true - если связь успешно добавлена, иначе - false.
   */
  public function addDestitute($name){
    if($this->getType() == self::VIRTUAL){
      throw new RuntimeException('Вируальный модуль не может иметь зависимости.');
    }
    else{
      return parent::addDestitute($name);
    }
  }

  /**
   * Метод удаляет зависимость от текущего компонента.
   * @param string $name Удаляемое имя зависимого компонента.
   * @throws InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws RuntimeException Выбрасывается в случае блокировки, отсутствия файла состояния компонента, ошибки при работе с потоком вывода или вызове метода для виртуального модуля.
   * @return boolean true - если связь успешно удалена, иначе - false.
   */
  public function removeDestitute($name){
    if($this->getType() == self::VIRTUAL){
      throw new RuntimeException('Вируальный модуль не может иметь зависимости.');
    }
    else{
      return parent::removeDestitute($name);
    }
  }

  /**
   * Метод возвращает массив используемых данным компонентом компонентов.
   * @throws RuntimeException Выбрасывается в случае, если вызываемый модуль не является конкретным.
   * @return string[] Массив имен используемых компонентов.
   */
  public function getUsed(){
    if($this->getType() == self::VIRTUAL){
      throw new RuntimeException('Вируальный модуль не может иметь зависимости.');
    }
    else{
      return parent::getUsed();
    }
  }

  /**
   * Метод возвращает контроллер данного модуля, если он является конкретным.
   * @throws RuntimeException Выбрасывается в случае, если модуль не явлется конкретным.
   * @return ModuleController Контроллер модуля.
   */
  public function getController(){
    if($this->getType() == self::VIRTUAL){
      throw new RuntimeException('Виртуальный модуль не имеет контроллер.');
    }
    $controller = str_replace('/', '\\', $this->getAddress()) . 'Controller';

    return $controller::getInstance();
  }

  /**
   * Метод возвращает объект класса инсталлятора модуля.
   * @return boolean|Installer Инсталлятор модуля или false если модуль не имеет его.
   */
  public function getInstaller(){
    if(file_exists($_SERVER['DOCUMENT_ROOT'] . $this->getAddress() . '/Installer.php')){
      $installer = str_replace('/', '\\', $this->getAddress()) . 'Installer';

      return $installer::getInstance();
    }
    else{
      return false;
    }
  }

  /**
   * Метод возвращает имя родительского модуля.
   * @return string Имя родительского модуля или false, если модуль не имеет родителя.
   */
  public function getParent(){
    $property = $this->ini->Depending_parent;

    return ($property != '')? $property : false;
  }

  /**
   * Метод возвращает массив имен дочерних модулей.
   * @return string[] Массив имен дочерних модулей.
   */
  public function getChild(){
    $modules = trim((string) $this->ini->Depending_children);
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
      $this->ini->Depending_children = implode(',', $child);
      try{
        $this->ini->rewrite();
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
      $this->ini->Depending_children = implode(',', $child);
      try{
        $this->ini->rewrite();
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
      throw new RuntimeException('Модуль [' . $this->getName() . '] не является конкретным и не может иметь правил доступа.');
    }
    if($this->ini->isSectionExists('Access')){
      $accesses = $this->ini->getSection('Access');
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