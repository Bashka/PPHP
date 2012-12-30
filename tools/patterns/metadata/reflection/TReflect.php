<?php
namespace PPHP\tools\patterns\metadata\reflection;

/**
 * Классическая реализация интерфейса PPHP\tools\patterns\metadata\reflection\Reflect.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\metadata\reflection
 */
trait TReflect{
  /**
   * Отражение класса.
   * @var array
   */
  static protected $reflectionClass = [];
  /**
   * Множество отражений свойств класса.
   * @var array
   */
  static protected $reflectionProperties = [];
  /**
   * Множество отражений методов класса.
   * @var array
   */
  static protected $reflectionMethods = [];

  /**
   * Метод возвращает представление свойства класса.
   * @static
   * @param string $propertyName Имя свойства.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return \PPHP\tools\patterns\metadata\reflection\ReflectionProperty Отображение свойства класса.
   */
  static public function &getReflectionProperty($propertyName){
    if(!is_string($propertyName) || empty($propertyName) || !property_exists(get_called_class(), $propertyName)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException();
    }
    $reflectionProperty = new ReflectionProperty(get_called_class(), $propertyName);
    $ownerClassName = $reflectionProperty->getDeclaringClass()->getName();
    if(!array_key_exists($ownerClassName, self::$reflectionProperties)){
      self::$reflectionProperties[$ownerClassName] = [];
    }
    if(!array_key_exists($propertyName, self::$reflectionProperties[$ownerClassName])){
      self::$reflectionProperties[$ownerClassName][$propertyName] = $reflectionProperty;
    }
    return self::$reflectionProperties[$ownerClassName][$propertyName];
  }

  /**
   * Метод возвращает представление метода класса.
   * @static
   * @param string $methodName Имя метода.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return \PPHP\tools\patterns\metadata\reflection\ReflectionMethod Отображение метода класса.
   */
  static public function &getReflectionMethod($methodName){
    if(!is_string($methodName) || empty($methodName) || !method_exists(get_called_class(), $methodName)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException();
    }
    $reflectionMethod = new ReflectionMethod(get_called_class(), $methodName);
    $ownerClassName = $reflectionMethod->getDeclaringClass()->getName();
    if(!array_key_exists($ownerClassName, self::$reflectionMethods)){
      self::$reflectionMethods[$ownerClassName] = [];
    }
    if(!array_key_exists($methodName, self::$reflectionMethods[$ownerClassName])){
      self::$reflectionMethods[$ownerClassName][$methodName] = $reflectionMethod;
    }
    return self::$reflectionMethods[$ownerClassName][$methodName];
  }

  /**
   * Метод возвращает представление класса.
   * @static
   * @return \PPHP\tools\patterns\metadata\reflection\ReflectionClass Отображение класса.
   */
  static public function &getReflectionClass(){
    if(!isset(self::$reflectionClass[get_called_class()])){
      self::$reflectionClass[get_called_class()] = new ReflectionClass(get_called_class());
    }
    return static::$reflectionClass[get_called_class()];
  }

  /**
   * Метод возвращает представление родительского класса.
   * @static
   * @return \PPHP\tools\patterns\metadata\reflection\ReflectionClass|null Отображение родительского класса или null - если данный класс является вершиной иерархии наследования.
   */
  static public function getParentReflectionClass(){
    $parentClass = static::getReflectionClass()->getParentClass();
    if(!$parentClass){
      return null;
    }
    $parentClass = $parentClass->getName();
    if(!$parentClass){
      return null;
    }
    if(!isset(self::$reflectionClass[$parentClass])){
      self::$reflectionClass[$parentClass] = new ReflectionClass($parentClass);
    }
    return static::$reflectionClass[$parentClass];
  }

  /**
   * Метод возвращает отображения всех свойств класса, в том числе видимых свойств родительского класса.
   * @static
   * @return \SplObjectStorage Отображение всех свойств класса.
   */
  static public function getAllReflectionProperties(){
    $reflectionProperties = new \SplObjectStorage();
    $namesAllProperties = static::getReflectionClass()->getProperties();
    foreach($namesAllProperties as $v){
      $reflectionProperties->attach(static::getReflectionProperty($v->getName()));
    }
    return $reflectionProperties;
  }

  /**
   * Метод возвращает отображения всех методов класса, в том числе видимых методов родительского класса.
   * @status
   * @return \SplObjectStorage Отображение всех методов класса.
   */
  static public function getAllReflectionMethods(){
    $reflectionMethods = new \SplObjectStorage();
    $namesAllMethods = static::getReflectionClass()->getMethods();
    foreach($namesAllMethods as $v){
      $reflectionMethods->attach(self::getReflectionMethod($v->getName()));
    }
    return $reflectionMethods;
  }
}
