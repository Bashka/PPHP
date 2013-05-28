<?php
namespace PPHP\services\configuration;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\classes\standard\fileSystem\ComponentFileSystem;
use PPHP\tools\classes\standard\fileSystem\FileINI;
use PPHP\tools\classes\standard\fileSystem\LockException;
use PPHP\tools\classes\standard\fileSystem\NotExistsException;
use PPHP\tools\patterns\io\IOException;
use \PPHP\tools\patterns\singleton as singleton;

/**
 * Класс служит для управления конфигурацией системы.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\services\configuration
 */
class Configurator implements singleton\Singleton{
use singleton\TSingleton;

  /**
   * Имя файла инициализации.
   */
  const iniFileName = 'conf.ini';

  /**
   * Файл конфигурации.
   * @var FileINI
   */
  protected $ini;

  /**
   * @throws NotExistsException Выбрасывается в случае, если требуемого файла конфигурации не найдено.
   */
  private function __construct(){
    $file = ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/services/configuration/' . self::iniFileName);
    if(!$file->isExists()){
      throw new NotExistsException('Файл конфигурации не найден.');
    }
    // Выброс исключений не предполагается
    $this->ini = new FileINI($file, true);
  }

  /**
   * Метод преобразует ссылку на ключ конфигурации в команду.
   * @param string $varName Ссылка на ключ конфигурации.
   * @return \stdClass Команда имеющая следующие свойства:
   * - section - секция конфигурации;
   * - key - ключ конфигурации.
   */
  protected function parseVarName($varName){
    $positionDelimiter = strpos($varName, '_');
    $result = new \stdClass();
    $result->section = substr($varName, 0, $positionDelimiter);
    $result->key = substr($varName, $positionDelimiter+1);
    return $result;
  }

  /**
   * Метод возвращает значение заданного свойства конфигурации.
   * @param string $section Секция конфигурации.
   * @param string $key Имя свойства.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае невозможности получения данных конфигурации.
   * @return null|string Значение свойства или null - если требуемого свойства не существует.
   */
  public function get($section, $key){
    exceptions\InvalidArgumentException::verifyType($section, 'S');
    exceptions\InvalidArgumentException::verifyType($key, 'S');
    if(!$this->ini->isDataExists($key, $section)){
      return null;
    }
    try{
      return (string) $this->ini->get($key, $section);
    }
    catch(LockException $e){
      throw new exceptions\NotFoundDataException('Невозможно получить доступ к конфигурации системы.', 1, $e);
    }
    catch(NotExistsException $e){
      throw new exceptions\NotFoundDataException('Невозможно получить доступ к конфигурации системы.', 1, $e);
    }
  }

  /**
   * Метод устанавливает новое свойство или изменяет существующее.
   * @param string $section Секция конфигурации.
   * @param string $key Имя свойства.
   * @param string $value Устанавливаемое значение.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае невозможности получения данных конфигурации.
   * @return boolean true - если изменения внесены успешно.
   */
  public function set($section, $key, $value){
    exceptions\InvalidArgumentException::verifyType($section, 'S');
    exceptions\InvalidArgumentException::verifyType($key, 'S');
    $value = (string)$value;
    try{
      $this->ini->set($key, $value, $section);
      $this->ini->rewrite();
    }
    catch(LockException $e){
      throw new exceptions\NotFoundDataException('Невозможно получить доступ к конфигурации системы.', 1, $e);
    }
    catch(NotExistsException $e){
      throw new exceptions\NotFoundDataException('Невозможно получить доступ к конфигурации системы.', 1, $e);
    }
    catch(IOException $e){
      throw new exceptions\NotFoundDataException('Невозможно получить доступ к конфигурации системы.', 1, $e);
    }
    return true;
  }

  /**
   * Метод определяет, имеется ли заданное свойство.
   * @param string $section Имя секции.
   * @param string $key Искомое свойство.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае невозможности получения данных конфигурации.
   * @return boolean true - если свойство имеется, иначе - false.
   */
  public function isExists($section, $key){
    exceptions\InvalidArgumentException::verifyType($section, 'S');
    exceptions\InvalidArgumentException::verifyType($key, 'S');
    try{
      return $this->ini->isDataExists($key, $section);
    }
    catch(LockException $e){
      throw new exceptions\NotFoundDataException('Невозможно получить доступ к конфигурации системы.', 1, $e);
    }
    catch(NotExistsException $e){
      throw new exceptions\NotFoundDataException('Невозможно получить доступ к конфигурации системы.', 1, $e);
    }
  }

  /**
   * Метод удаляет заданное свойство.
   * @param string $section Секция конфигурации.
   * @param string $key Имя свойства.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае невозможности получения данных конфигурации.
   * @return boolean true - если свойство удалено успешно.
   */
  public function delete($section, $key){
    exceptions\InvalidArgumentException::verifyType($section, 'S');
    exceptions\InvalidArgumentException::verifyType($key, 'S');
    try{
      $this->ini->remove($key, $section);
      $this->ini->rewrite();
    }
    catch(LockException $e){
      throw new exceptions\NotFoundDataException('Невозможно получить доступ к конфигурации системы.', 1, $e);
    }
    catch(NotExistsException $e){
      throw new exceptions\NotFoundDataException('Невозможно получить доступ к конфигурации системы.', 1, $e);
    }
    catch(IOException $e){
      throw new exceptions\NotFoundDataException('Невозможно получить доступ к конфигурации системы.', 1, $e);
    }
    return true;
  }

  /**
   * Метод возвращает содержимое указанной секции конфигурации.
   * @param string $section Имя целевой секции.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае невозможности получения данных конфигурации.
   * @return array Содержимое целевой секции.
   */
  public function getSection($section){
    exceptions\InvalidArgumentException::verifyType($section, 'S');
    try{
      return $this->ini->getSection($section);
    }
    catch(LockException $e){
      throw new exceptions\NotFoundDataException('Невозможно получить доступ к конфигурации системы.', 1, $e);
    }
    catch(NotExistsException $e){
      throw new exceptions\NotFoundDataException('Невозможно получить доступ к конфигурации системы.', 1, $e);
    }
  }

  function __get($name){
    $name = $this->parseVarName($name);
    return $this->get($name->section, $name->key);
  }

  function __set($name, $value){
    $name = $this->parseVarName($name);
    $this->set($name->section, $name->key, $value);
  }

  function __isset($name){
    $name = $this->parseVarName($name);
    return $this->isExists($name->section, $name->key);
  }

  function __unset($name){
    $name = $this->parseVarName($name);
    $this->delete($name->section, $name->key);
  }
}
