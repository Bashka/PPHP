<?php
namespace PPHP\services\configuration;

/**
 * Класс служит для управления конфигурацией системы
 */
class Configurator implements \PPHP\tools\patterns\singleton\Singleton{
use \PPHP\tools\patterns\singleton\TSingleton;

  /**
   * Имя файла инициализации.
   */
  const iniFileName = 'conf.ini';

  /**
   * Файл конфигурации.
   * @var \PPHP\tools\classes\standard\fileSystem\FileINI
   */
  protected $ini;

  /**
   * @throws \PPHP\services\InitializingDataNotFoundException Выбрасывается в случае, если требуемого файла конфигурации не найдено.
   */
  private function __construct(){
    $file = \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/services/configuration/' . self::iniFileName);
    if(!$file->isExists()){
      throw new \PPHP\services\InitializingDataNotFoundException('Файл конфигурации не найден.');
    }
    $this->ini = new \PPHP\tools\classes\standard\fileSystem\FileINI($file, true);
  }

  /**
   * Метод возвращает значение заданного свойства конфигурации.
   * @param string $section Секция конфигурации.
   * @param string $key Имя свойства.
   * @return boolean|string Значение свойства или false - если требуемого свойства не существует.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException
   */
  public function get($section, $key){
    if(!is_string($section) || empty($section)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $section);
    }
    if(!is_string($key) || empty($key)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $key);
    }
    if(!$this->ini->isDataExists($key, $section)){
      return false;
    }
    return (string) $this->ini->get($key, $section);
  }

  /**
   * Метод устанавливает новое свойство или изменяет существующее.
   * @param string $section Секция конфигурации.
   * @param string $key Имя свойства.
   * @param string $value Устанавливаемое значение.
   * @return boolean true - если изменения внесены успешно.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException
   */
  public function set($section, $key, $value){
    if(!is_string($section) || empty($section)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $section);
    }
    if(!is_string($key) || empty($key)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $key);
    }
    $value = (string)$value;
    $this->ini->set($key, $value, $section);
    $this->ini->rewrite();
    return true;
  }

  /**
   * Метод определяет, имеется ли заданное свойство.
   * @param string $section Имя секции.
   * @param string $key Искомое свойство.
   * @return boolean true - если свойство имеется, иначе - false.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException
   */
  public function isExists($section, $key){
    if(!is_string($section) || empty($section)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $section);
    }
    if(!is_string($key) || empty($key)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $key);
    }
    return $this->ini->isDataExists($key, $section);
  }

  /**
   * Метод удаляет заданное свойство.
   * @param string $section Секция конфигурации.
   * @param string $key Имя свойства.
   * @return boolean true - если свойство удалено успешно.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException
   */
  public function delete($section, $key){
    if(!is_string($section) || empty($section)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $section);
    }
    if(!is_string($key) || empty($key)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $key);
    }
    $this->ini->remove($key, $section);
    $this->ini->rewrite();
    return true;
  }

  /**
   * Метод возвращает содержимое указанной секции конфигурации.
   * @param string $section Имя целевой секции.
   * @return array Содержимое целевой секции.
   */
  public function getSection($section){
    return $this->ini->getSection($section);
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
