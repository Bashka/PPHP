<?php
namespace PPHP\model\modules\SystemPackages;

use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\baseType\exceptions\RuntimeException;
use PPHP\tools\classes\standard\baseType\exceptions\StructureException;
use PPHP\tools\classes\standard\fileSystem\ComponentFileSystem;
use PPHP\tools\classes\standard\fileSystem\FileINI;
use PPHP\tools\classes\standard\fileSystem\LockException;
use PPHP\tools\classes\standard\fileSystem\NotExistsException;
use PPHP\tools\patterns\io\IOException;
use PPHP\tools\patterns\metadata as metadata;

/**
 * Отражение компонента системы.
 * Объекты данного класса являются отражениями компонентов системы и предоставляют информацию о них.
 * @package PPHP\model\modules\SystemPackages
 */
abstract class ReflectionSystemComponent implements metadata\Described{
  use metadata\TDescribed;

  /**
   * Имя файла состояния компонента.
   */
  const STATE_FILE_NAME = 'state.ini';

  /**
   * Имя компонента.
   * @var string
   */
  protected $name;

  /**
   * Файл состояния компонента.
   * @var FileINI
   */
  protected $ini;

  /**
   * @param string $name Имя компонента.
   * @throws SystemComponentNotFoundException Выбрасывается в случае, если требуемый компонент, хранящий его каталог или файл состояния не найдены.
   */
  public function __construct($name){
    $this->name = $name;
    $a = $_SERVER['DOCUMENT_ROOT'] . $this->getAddress() . self::STATE_FILE_NAME;
    $this->ini = ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . $this->getAddress() . self::STATE_FILE_NAME);
    try{
      if(!$this->ini->isExists()){
        throw new SystemComponentNotFoundException('Требуемый файл состояния модуля [' . $this->name . '] не найден.');
      }
    }
    catch(NotExistsException $e){
      throw new SystemComponentNotFoundException('Каталог компонента [' . $this->name . '] не найден.', 1, $e);
    }
    $this->ini = new FileINI($this->ini, true);
  }

  /**
   * Метод возвращает имя компонента.
   * @return string Имя компонента.
   */
  public function getName(){
    return $this->name;
  }

  /**
   * Метод возвращает физический адрес каталога компонента от корня системы. Адрес начинается и заканчивается символами косой черты (/).
   * @return string Физический адрес каталога компонента от корня системы.
   */
  public abstract function getAddress();

  /**
   * Метод возвращает версию компонента.
   * @throws StructureException Выбрасывается в случает отсутствия обязательного свойства модуля.
   * @return string Версия компонента.
   */
  public function getVersion(){
    $property = $this->ini->Component_version;
    if(is_null($property)){
      throw new StructureException('Недопустимая структура файла состояния компонента. Отсутствие обязательного свойства [Component::version].');
    }

    return $property;
  }

  /**
   * Метод возвращает массив имен компонентов, зависимых от данного.
   * @return string[] Массив имен зависимых компонентов.
   */
  public function getDestitute(){
    $components = trim((string) $this->ini->Depending_destitute);
    if($components == ''){
      $components = [];
    }
    else{
      $components = explode(',', $components);
    }

    return $components;
  }

  /**
   * Метод добавляет зависимость от текущего компонента.
   * @param string $name Добавляемое имя зависимого компонента.
   * @throws InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws RuntimeException Выбрасывается в случае блокировки, отсутствия файла состояния компонента или ошибки при работе с потоком вывода.
   * @return boolean true - если связь успешно добавлена, иначе - false.
   */
  public function addDestitute($name){
    InvalidArgumentException::verifyType($name, 'S');
    $destitute = $this->getDestitute();
    if(array_search($name, $destitute) === false){
      $destitute[] = $name;
      $this->ini->Depending_destitute = implode(',', $destitute);
      try{
        $this->ini->rewrite();
      }
      catch(LockException $e){
        throw new RuntimeException('Отсутствует доступ к файлу состояния компонента.', 1, $e);
      }
      catch(NotExistsException $e){
        throw new RuntimeException('Отсутствует доступ к файлу состояния компонента.', 1, $e);
      }
      catch(IOException $e){
        throw new RuntimeException('Отсутствует доступ к файлу состояния компонента.', 1, $e);
      }

      return true;
    }

    return false;
  }

  /**
   * Метод удаляет зависимость от текущего компонента.
   * @param string $name Удаляемое имя зависимого компонента.
   * @throws InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws RuntimeException Выбрасывается в случае блокировки, отсутствия файла состояния компонента или ошибки при работе с потоком вывода.
   * @return boolean true - если связь успешно удалена, иначе - false.
   */
  public function removeDestitute($name){
    InvalidArgumentException::verifyType($name, 'S');
    $destitute = $this->getDestitute();
    if(($key = array_search($name, $destitute)) !== false){
      unset($destitute[$key]);
      $this->ini->Depending_destitute = implode(',', $destitute);
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
   * Метод возвращает массив используемых данным компонентом компонентов.
   * @return string[] Массив имен используемых компонентов.
   */
  public function getUsed(){
    $modules = trim((string) $this->ini->Depending_used);
    if($modules == ''){
      $modules = [];
    }
    else{
      $modules = explode(',', $modules);
    }

    return $modules;
  }
}