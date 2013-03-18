<?php
namespace PPHP\tools\patterns\metadata;

/**
 * Данный класс добавляет некоторые дополнительные методы для управления метаданными.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\metadata
 */
class MetadataManager{
  /**
   * Объект, чей класс управляется.
   * @var string
   */
  private $className;

  function __construct($className){
    if(!is_string($className)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $className);
    }
    $this->className = $className;
  }

  /**
   * Метод возвращает представление свойства управляемого класса.
   * @static
   * @param string $className Управляемый класс.
   * @param string $propertyName Имя свойства.
   * @return \PPHP\tools\patterns\metadata\reflection\ReflectionProperty
   */
  static public function &getReflectionPropertyClass($className, $propertyName){
    return $className::getReflectionProperty($propertyName);
  }

  /**
   * Метод возвращает представление метода управляемого класса.
   * @static
   * @param string $className Управляемый класс.
   * @param $methodName Имя метода.
   * @return \PPHP\tools\patterns\metadata\reflection\ReflectionMethod
   */
  static public function &getReflectionMethodClass($className, $methodName){
    return $className::getReflectionMethod($methodName);
  }

  /**
   * Метод возвращает представление управляемого класса.
   * @static
   * @param $className Управляемый класс.
   * @return \PPHP\tools\patterns\metadata\reflection\ReflectionClass
   */
  static public function &getReflectionClassClass($className){
    return $className::getReflectionClass();
  }

  /**
   * Метод возвращает отображения всех свойств управляемого класса.
   * @static
   * @param $className Управляемый класс.
   * @return \SplObjectStorage
   */
  static public function getAllReflectionPropertiesClass($className){
    return $className::getAllReflectionProperties();
  }

  /**
   * Метод возвращает отображения всех методов управляемого класса.
   * @static
   * @param $className Управляемый класс.
   * @return \SplObjectStorage
   */
  static public function getAllReflectionMethodsClass($className){
    return $className::getAllReflectionMethods();
  }

  /**
   * Метод устанавливает метаданные свойству класса.
   * @static
   * @param $className Управляемый класс.
   * @param string $propertyName Имя свойства.
   * @param string $metadataName Имя метаданных.
   * @param string $metadataValue Значение метаданных.
   */
  static public function setMetadataPropertyClass($className, $propertyName, $metadataName, $metadataValue){
    $className::getReflectionProperty($propertyName)->setMetadata($metadataName, $metadataValue);
  }

  /**
   * Метод устанавливает метаданные методу класса.
   * @static
   * @param $className Управляемый класс.
   * @param string $methodName Имя метода.
   * @param string $metadataName Имя метаданных.
   * @param string $metadataValue Значение метаданных.
   */
  static public function setMetadataMethodClass($className, $methodName, $metadataName, $metadataValue){
    $className::getReflectionMethod($methodName)->setMetadata($metadataName, $metadataValue);
  }

  /**
   * Метод устанавливает метаданные классу.
   * @static
   * @param $className Управляемый класс.
   * @param string $metadataName Имя метаданных.
   * @param string $metadataValue Значение метаданных.
   */
  static public function setMetadataClassClass($className, $metadataName, $metadataValue){
    $className::getReflectionClass()->setMetadata($metadataName, $metadataValue);
  }

  /**
   * Метод получает метаданные свойства класса.
   * @static
   * @param $className Управляемый класс.
   * @param string $propertyName Имя свойства.
   * @param string $metadataName Имя метаданных.
   * @return null|string Значение метаданных.
   */
  static public function getMetadataPropertyClass($className, $propertyName, $metadataName){
    return $className::getReflectionProperty($propertyName)->getMetadata($metadataName);
  }

  /**
   * Метод получает метаданные метода класса.
   * @static
   * @param $className Управляемый класс.
   * @param string $methodName Имя метода.
   * @param string $metadataName Имя метаданных.
   * @return null|string Значение метаданных.
   */
  static public function getMetadataMethodClass($className, $methodName, $metadataName){
    return $className::getReflectionMethod($methodName)->getMetadata($metadataName);
  }

  /**
   * Метод получает метаданные класса.
   * @static
   * @param $className Управляемый класс.
   * @param string $metadataName Имя метаданных.
   * @return null|string Значение метаданных.
   */
  static public function getMetadataClassClass($className, $metadataName){
    return $className::getReflectionClass()->getMetadata($metadataName);
  }

  /**
   * Метод удаляет метаданные свойства класса.
   * @static
   * @param $className Управляемый класс.
   * @param string $propertyName Имя свойства.
   * @param string $metadataName Имя метаданных.
   * @return
   */
  static public function removeMetadataPropertyClass($className, $propertyName, $metadataName){
    return $className::getReflectionProperty($propertyName)->removeMetadata($metadataName);
  }

  /**
   * Метод удаляет метаданные метода класса.
   * @static
   * @param $className Управляемый класс.
   * @param string $methodName Имя метода.
   * @param string $metadataName Имя метаданных.
   * @return
   */
  static public function removeMetadataMethodClass($className, $methodName, $metadataName){
    return $className::getReflectionMethod($methodName)->removeMetadata($metadataName);
  }

  /**
   * Метод определяет, имеются ли заданный набор метаданных в отображении.
   * @static
   * @param \PPHP\tools\patterns\metadata\Described $v Проверяемое отображение.
   * @param array $requiredMetadata Массив имен метаданных, которые должны быть установлены в отображении.
   * @return boolean true - если все метаданные присуствуют, иначе - false,
   */
  static private function filterAndMetadata(\PPHP\tools\patterns\metadata\Described $v, array $requiredMetadata){
    foreach($requiredMetadata as $requiredData){
      if(!$v->isMetadataExists($requiredData)){
        return false;
      }
    }
    return true;
  }

  /**
   * Метод возвращает все отображения свойств класса, которые имеют указанные в аргументах метаданные.
   * @static
   * @param string $className Управляемый класс.
   * @param string ... Список требуемых метаданных.
   * @return \SplObjectStorage Множество отображений свойств класса, имеющих указанные метаданные.
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
   * Метод возвращает все отображения методов класса, которые имеют указанные в аргументах метаданные.
   * @static
   * @param string $className Управляемый класс.
   * @param string ... Список требуемых метаданных.
   * @return \SplObjectStorage Множество отображений методов класса, имеющих указанные метаданные.
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
   * Метод возвращает представление свойства управляемого класса.
   * @param string $propertyName Имя свойства.
   * @return \PPHP\tools\patterns\metadata\reflection\ReflectionProperty
   */
  public function &getReflectionProperty($propertyName){
    return self::getReflectionPropertyClass($this->className, $propertyName);
  }

  /**
   * Метод возвращает представление метода управляемого класса.
   * @param $methodName Имя метода.
   * @return \PPHP\tools\patterns\metadata\reflection\ReflectionMethod
   */
  public function &getReflectionMethod($methodName){
    return self::getReflectionMethodClass($this->className, $methodName);
  }

  /**
   * Метод возвращает представление управляемого класса.
   * @return \PPHP\tools\patterns\metadata\reflection\ReflectionClass
   */
  public function &getReflectionClass(){
    return self::getReflectionClassClass($this->className);
  }

  /**
   * Метод возвращает отображения всех свойств управляемого класса.
   * @return \SplObjectStorage
   */
  public function getAllReflectionProperties(){
    return self::getAllReflectionPropertiesClass($this->className);
  }

  /**
   * Метод возвращает отображения всех методов управляемого класса.
   * @return \SplObjectStorage
   */
  public function getAllReflectionMethods(){
    return self::getAllReflectionMethodsClass($this->className);
  }

  /**
   * Метод устанавливает метаданные свойству класса.
   * @param string $propertyName Имя свойства.
   * @param string $metadataName Имя метаданных.
   * @param string $metadataValue Значение метаданных.
   */
  public function setMetadataProperty($propertyName, $metadataName, $metadataValue){
    self::setMetadataPropertyClass($this->className, $propertyName, $metadataName, $metadataValue);
  }

  /**
   * Метод устанавливает метаданные методу класса.
   * @param string $methodName Имя метода.
   * @param string $metadataName Имя метаданных.
   * @param string $metadataValue Значение метаданных.
   */
  public function setMetadataMethod($methodName, $metadataName, $metadataValue){
    self::setMetadataMethodClass($this->className, $methodName, $metadataName, $metadataValue);
  }

  /**
   * Метод устанавливает метаданные классу.
   * @param string $metadataName Имя метаданных.
   * @param string $metadataValue Значение метаданных.
   */
  public function setMetadataClass($metadataName, $metadataValue){
    self::setMetadataClassClass($this->className, $metadataName, $metadataValue);
  }

  /**
   * Метод получает метаданные свойства класса.
   * @param string $propertyName Имя свойства.
   * @param string $metadataName Имя метаданных.
   * @return null|string Значение метаданных.
   */
  public function getMetadataProperty($propertyName, $metadataName){
    return self::getMetadataPropertyClass($this->className, $propertyName, $metadataName);
  }

  /**
   * Метод получает метаданные метода класса.
   * @param string $propertyName Имя метода.
   * @param string $metadataName Имя метаданных.
   * @return null|string Значение метаданных.
   */
  public function getMetadataMethod($propertyName, $metadataName){
    return self::getMetadataMethodClass($this->className, $propertyName, $metadataName);
  }

  /**
   * Метод получает метаданные класса.
   * @param string $metadataName Имя метаданных.
   * @return null|string Значение метаданных.
   */
  public function getMetadataClass($metadataName){
    return self::getMetadataClassClass($this->className, $metadataName);
  }

  /**
   * Метод удаляет метаданные свойства класса.
   * @param string $propertyName Имя свойства.
   * @param string $metadataName Имя метаданных.
   * @return
   */
  public function removeMetadataProperty($propertyName, $metadataName){
    return self::removeMetadataPropertyClass($this->className, $propertyName, $metadataName);
  }

  /**
   * Метод удаляет метаданные метода класса.
   * @param string $methodName Имя метода.
   * @param string $metadataName Имя метаданных.
   * @return
   */
  public function removeMetadataMethod($methodName, $metadataName){
    return self::removeMetadataMethodClass($this->className, $methodName, $metadataName);
  }

  /**
   * Метод возвращает все отображения свойств класса, которые имеют указанные в аргументах метаданные.
   * @param string ... Список требуемых метаданных.
   * @return \SplObjectStorage Множество отображений свойств класса, имеющих указанные метаданные.
   */
  public function getAllReflectionPropertiesWithMetadata(){
    $args = func_get_args();
    array_unshift($args, $this->className);
    return call_user_func_array([get_class(), 'getAllReflectionPropertiesWithMetadataClass'], $args);
  }

  /**
   * Метод возвращает все отображения методов класса, которые имеют указанные в аргументах метаданные.
   * @param string ... Список требуемых метаданных.
   * @return \SplObjectStorage Множество отображений методов класса, имеющих указанные метаданные.
   */
  public function getAllReflectionMethodsWithMetadata(){
    $args = func_get_args();
    array_unshift($args, $this->className);
    return call_user_func_array([get_class(), 'getAllReflectionMethodsWithMetadataClass'], $args);
  }
}
