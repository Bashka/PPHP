<?php
namespace PPHP\tools\patterns\metadata;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Данный класс добавляет некоторые дополнительные методы для управления метаданными.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\metadata
 */
class MetadataManager{
  /**
   * Имя управляемого класса.
   * @var string
   */
  private $className;

  function __construct($className){
    if(!is_string($className)){
      throw new exceptions\InvalidArgumentException('string', $className);
    }
    $this->className = $className;
  }

  /**
   * Метод возвращает отражение свойства управляемого класса.
   * @static
   *
   * @param string $className    Управляемый класс.
   * @param string $propertyName Имя свойства.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return reflection\ReflectionProperty Отражение указанного свойства.
   */
  static public function &getReflectionPropertyClass($className, $propertyName){
    try{
      return $className::getReflectionProperty($propertyName);
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * Метод возвращает отражение метода управляемого класса.
   * @static
   *
   * @param string $className  Управляемый класс.
   * @param        $methodName Имя метода.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return reflection\ReflectionMethod Отражение указанного метода.
   */
  static public function &getReflectionMethodClass($className, $methodName){
    try{
      return $className::getReflectionMethod($methodName);
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * Метод возвращает отражение управляемого класса.
   * @static
   *
   * @param $className Управляемый класс.
   *
   * @return reflection\ReflectionClass Отражение управляемого класса.
   */
  static public function &getReflectionClassClass($className){
    return $className::getReflectionClass();
  }

  /**
   * Метод возвращает отражения всех свойств управляемого класса.
   * @static
   *
   * @param $className Управляемый класс.
   *
   * @return \SplObjectStorage Отражения всех свойств управляемого класса.
   */
  static public function getAllReflectionPropertiesClass($className){
    return $className::getAllReflectionProperties();
  }

  /**
   * Метод возвращает отображения всех методов управляемого класса.
   * @static
   *
   * @param $className Управляемый класс.
   *
   * @return \SplObjectStorage Отражения всех методов управляемого класса.
   */
  static public function getAllReflectionMethodsClass($className){
    return $className::getAllReflectionMethods();
  }

  /**
   * Метод устанавливает метаданные свойству класса.
   * @static
   *
   * @param        $className     Управляемый класс.
   * @param string $propertyName  Имя свойства.
   * @param string $metadataName  Имя метаданных.
   * @param string $metadataValue Значение метаданных.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  static public function setMetadataPropertyClass($className, $propertyName, $metadataName, $metadataValue){
    try{
      $className::getReflectionProperty($propertyName)->setMetadata($metadataName, $metadataValue);
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * Метод устанавливает метаданные методу класса.
   * @static
   *
   * @param        $className     Управляемый класс.
   * @param string $methodName    Имя метода.
   * @param string $metadataName  Имя метаданных.
   * @param string $metadataValue Значение метаданных.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  static public function setMetadataMethodClass($className, $methodName, $metadataName, $metadataValue){
    try{
      $className::getReflectionMethod($methodName)->setMetadata($metadataName, $metadataValue);
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * Метод устанавливает метаданные классу.
   * @static
   *
   * @param        $className     Управляемый класс.
   * @param string $metadataName  Имя метаданных.
   * @param string $metadataValue Значение метаданных.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  static public function setMetadataClassClass($className, $metadataName, $metadataValue){
    try{
      $className::getReflectionClass()->setMetadata($metadataName, $metadataValue);
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * Метод получает метаданные свойства класса.
   * @static
   *
   * @param        $className    Управляемый класс.
   * @param string $propertyName Имя свойства.
   * @param string $metadataName Имя метаданных.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return null|string Значение метаданных.
   */
  static public function getMetadataPropertyClass($className, $propertyName, $metadataName){
    try{
      return $className::getReflectionProperty($propertyName)->getMetadata($metadataName);
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * Метод получает метаданные метода класса.
   * @static
   *
   * @param        $className    Управляемый класс.
   * @param string $methodName   Имя метода.
   * @param string $metadataName Имя метаданных.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return null|string Значение метаданных.
   */
  static public function getMetadataMethodClass($className, $methodName, $metadataName){
    try{
      return $className::getReflectionMethod($methodName)->getMetadata($metadataName);
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * Метод получает метаданные класса.
   * @static
   *
   * @param        $className    Управляемый класс.
   * @param string $metadataName Имя метаданных.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return null|string Значение метаданных.
   */
  static public function getMetadataClassClass($className, $metadataName){
    try{
      return $className::getReflectionClass()->getMetadata($metadataName);
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * Метод удаляет метаданные свойства класса.
   * @static
   *
   * @param        $className    Управляемый класс.
   * @param string $propertyName Имя свойства.
   * @param string $metadataName Имя метаданных.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  static public function removeMetadataPropertyClass($className, $propertyName, $metadataName){
    try{
      $className::getReflectionProperty($propertyName)->removeMetadata($metadataName);
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * Метод удаляет метаданные метода класса.
   * @static
   *
   * @param        $className    Управляемый класс.
   * @param string $methodName   Имя метода.
   * @param string $metadataName Имя метаданных.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  static public function removeMetadataMethodClass($className, $methodName, $metadataName){
    try{
      $className::getReflectionMethod($methodName)->removeMetadata($metadataName);
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * Метод определяет, имеются ли заданный набор метаданных в отражении.
   * @static
   *
   * @param Described $v                Проверяемое отражение.
   * @param array     $requiredMetadata Массив имен метаданных, которые должны быть установлены в отражении.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return boolean true - если все метаданные присуствуют, иначе - false,
   */
  static private function filterAndMetadata(Described $v, array $requiredMetadata){
    foreach($requiredMetadata as $requiredData){
      try{
        if(!$v->isMetadataExists($requiredData)){
          return false;
        }
      }
      catch(exceptions\InvalidArgumentException $exc){
        throw $exc;
      }
    }
    return true;
  }

  /**
   * Метод возвращает все отражения свойств класса, которые имеют указанные в параметрах метаданные.
   * @static
   *
   * @param string $className Управляемый класс.
   * @param        string     ... Список требуемых метаданных.
   *
   * @return \SplObjectStorage Множество отражений свойств класса, имеющих указанные метаданные.
   */
  static public function getAllReflectionPropertiesWithMetadataClass($className){
    $argv = func_get_args();
    array_shift($argv);
    $allRef = $className::getAllReflectionProperties();
    $resultRef = new \SplObjectStorage();

    foreach($allRef as $ref){
      if(self::filterAndMetadata($ref, $argv)){
        $resultRef->attach($ref);
      }
    }
    return $resultRef;
  }

  /**
   * Метод возвращает все отражения методов класса, которые имеют указанные в параметрах метаданные.
   * @static
   *
   * @param string $className Управляемый класс.
   * @param        string     ... Список требуемых метаданных.
   *
   * @return \SplObjectStorage Множество отражений методов класса, имеющих указанные метаданные.
   */
  static public function getAllReflectionMethodsWithMetadataClass($className){
    $argv = func_get_args();
    array_shift($argv);
    $allRef = $className::getAllReflectionMethods();
    $resultRef = new \SplObjectStorage();

    foreach($allRef as $ref){
      if(self::filterAndMetadata($ref, $argv)){
        $resultRef->attach($ref);
      }
    }
    return $resultRef;
  }

  /**
   * Метод возвращает отражение свойства управляемого класса.
   * @param string $propertyName Имя свойства.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return reflection\ReflectionProperty Запрашиваемое отражение.
   */
  public function &getReflectionProperty($propertyName){
    try{
      return self::getReflectionPropertyClass($this->className, $propertyName);
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * Метод возвращает отражение метода управляемого класса.
   * @param $methodName Имя метода.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return reflection\ReflectionMethod Запрашиваемое отражение.
   */
  public function &getReflectionMethod($methodName){
    try{
      return self::getReflectionMethodClass($this->className, $methodName);
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * Метод возвращает отражение управляемого класса.
   * @return \PPHP\tools\patterns\metadata\reflection\ReflectionClass
   */
  public function &getReflectionClass(){
    return self::getReflectionClassClass($this->className);
  }

  /**
   * Метод возвращает отражения всех свойств управляемого класса.
   * @return \SplObjectStorage
   */
  public function getAllReflectionProperties(){
    return self::getAllReflectionPropertiesClass($this->className);
  }

  /**
   * Метод возвращает отражения всех методов управляемого класса.
   * @return \SplObjectStorage
   */
  public function getAllReflectionMethods(){
    return self::getAllReflectionMethodsClass($this->className);
  }

  /**
   * Метод устанавливает метаданные свойству класса.
   *
   * @param string $propertyName  Имя свойства.
   * @param string $metadataName  Имя метаданных.
   * @param string $metadataValue Значение метаданных.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  public function setMetadataProperty($propertyName, $metadataName, $metadataValue){
    try{
      self::setMetadataPropertyClass($this->className, $propertyName, $metadataName, $metadataValue);
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * Метод устанавливает метаданные методу класса.
   *
   * @param string $methodName    Имя метода.
   * @param string $metadataName  Имя метаданных.
   * @param string $metadataValue Значение метаданных.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  public function setMetadataMethod($methodName, $metadataName, $metadataValue){
    try{
      self::setMetadataMethodClass($this->className, $methodName, $metadataName, $metadataValue);
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * Метод устанавливает метаданные классу.
   *
   * @param string $metadataName  Имя метаданных.
   * @param string $metadataValue Значение метаданных.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  public function setMetadataClass($metadataName, $metadataValue){
    try{
      self::setMetadataClassClass($this->className, $metadataName, $metadataValue);
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * Метод получает метаданные свойства класса.
   *
   * @param string $propertyName Имя свойства.
   * @param string $metadataName Имя метаданных.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return null|string Значение метаданных.
   */
  public function getMetadataProperty($propertyName, $metadataName){
    try{
      return self::getMetadataPropertyClass($this->className, $propertyName, $metadataName);
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * Метод получает метаданные метода класса.
   *
   * @param string $propertyName Имя метода.
   * @param string $metadataName Имя метаданных.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return null|string Значение метаданных.
   */
  public function getMetadataMethod($propertyName, $metadataName){
    try{
      return self::getMetadataMethodClass($this->className, $propertyName, $metadataName);
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * Метод получает метаданные класса.
   *
   * @param string $metadataName Имя метаданных.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return null|string Значение метаданных.
   */
  public function getMetadataClass($metadataName){
    try{
      return self::getMetadataClassClass($this->className, $metadataName);
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * Метод удаляет метаданные свойства класса.
   *
   * @param string $propertyName Имя свойства.
   * @param string $metadataName Имя метаданных.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  public function removeMetadataProperty($propertyName, $metadataName){
    try{
      return self::removeMetadataPropertyClass($this->className, $propertyName, $metadataName);
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * Метод удаляет метаданные метода класса.
   *
   * @param string $methodName   Имя метода.
   * @param string $metadataName Имя метаданных.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  public function removeMetadataMethod($methodName, $metadataName){
    try{
      return self::removeMetadataMethodClass($this->className, $methodName, $metadataName);
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * Метод возвращает все отражения свойств класса, которые имеют указанные в аргументах метаданные.
   *
   * @param string ... Список требуемых метаданных.
   *
   * @return \SplObjectStorage Множество отражений свойств класса, имеющих указанные метаданные.
   */
  public function getAllReflectionPropertiesWithMetadata(){
    $args = func_get_args();
    array_unshift($args, $this->className);
    return call_user_func_array([get_class(), 'getAllReflectionPropertiesWithMetadataClass'], $args);
  }

  /**
   * Метод возвращает все отражения методов класса, которые имеют указанные в аргументах метаданные.
   * @param string ... Список требуемых метаданных.
   *
   * @return \SplObjectStorage Множество отражений методов класса, имеющих указанные метаданные.
   */
  public function getAllReflectionMethodsWithMetadata(){
    $args = func_get_args();
    array_unshift($args, $this->className);
    return call_user_func_array([get_class(), 'getAllReflectionMethodsWithMetadataClass'], $args);
  }
}
