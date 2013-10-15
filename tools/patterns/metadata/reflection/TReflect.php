<?php
namespace PPHP\tools\patterns\metadata\reflection;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Классическая реализация интерфейса Reflect.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\metadata\reflection
 */
trait TReflect{
  /**
   * Отражения классов.
   * Свойство включает отражения классов во всей иерархии наследования данного класса.
   * В качестве ключа массива используется имя класса, а в качестве значения, его отражение.
   * @var ReflectionClass[]
   */
  static protected $reflectionClass = [];

  /**
   * Множество отражений свойств классов.
   * Свойство включает отражения свойств классов во всей иерархии наследования данного класса.
   * В качестве ключа массива используется имя класса, в котором декларировано свойство, а в качестве значения, его отражение.
   * @var ReflectionProperty[][]
   */
  static protected $reflectionProperties = [];

  /**
   * Множество отражений методов классов.
   * Свойство включает отражения методов классов во всей иерархии наследования данного класса.
   * В качестве ключа массива используется имя класса, в котором декларирован метод, а в качестве значения, его отражение.
   * @var ReflectionMethod[][]
   */
  static protected $reflectionMethods = [];

  /**
   * @prototype \PPHP\tools\patterns\metadata\reflection\Reflect
   */
  static public function &getReflectionProperty($name){
    exceptions\InvalidArgumentException::verifyType($name, 'S');
    /**
     * @var Reflect $class
     */
    $class = get_called_class();
    while(!property_exists($class, $name)){
      $parentClass = $class::getReflectionClass()->getParentClass();
      if($parentClass === false){
        throw new exceptions\ComponentClassException('Указанное свойство [' . $name . '] отсутствует в вызываемом классе и его надклассах.');
      }
      $class = $parentClass->getName();
    }
    $reflectionProperty = new ReflectionProperty($class, $name);
    // Проверка отношения получаемого отражения к классам в иерархии наследования
    $ownerClassName = $reflectionProperty->getDeclaringClass()->getName();
    if(!array_key_exists($ownerClassName, self::$reflectionProperties)){
      self::$reflectionProperties[$ownerClassName] = [];
    }
    if(!array_key_exists($name, self::$reflectionProperties[$ownerClassName])){
      self::$reflectionProperties[$ownerClassName][$name] = $reflectionProperty;
    }

    return self::$reflectionProperties[$ownerClassName][$name];
  }

  /**
   * @prototype \PPHP\tools\patterns\metadata\reflection\Reflect
   */
  static public function &getReflectionMethod($name){
    exceptions\InvalidArgumentException::verifyType($name, 'S');
    /**
     * @var Reflect $class
     */
    $class = get_called_class();
    while(!method_exists($class, $name)){
      $parentClass = $class::getReflectionClass()->getParentClass();
      if($parentClass === false){
        throw new exceptions\ComponentClassException('Указанный метод [' . $name . '] отсутствует в вызываемом классе и его надклассах.');
      }
      $class = $parentClass->getName();
    }
    $reflectionMethod = new ReflectionMethod($class, $name);
    // Проверка отношения получаемого отражения к классам в иерархии наследования
    $ownerClassName = $reflectionMethod->getDeclaringClass()->getName();
    if(!array_key_exists($ownerClassName, self::$reflectionMethods)){
      self::$reflectionMethods[$ownerClassName] = [];
    }
    if(!array_key_exists($name, self::$reflectionMethods[$ownerClassName])){
      self::$reflectionMethods[$ownerClassName][$name] = $reflectionMethod;
    }

    return self::$reflectionMethods[$ownerClassName][$name];
  }

  /**
   * @prototype \PPHP\tools\patterns\metadata\reflection\Reflect
   */
  static public function &getReflectionClass(){
    if(!isset(self::$reflectionClass[get_called_class()])){
      self::$reflectionClass[get_called_class()] = new ReflectionClass(get_called_class());
    }

    return static::$reflectionClass[get_called_class()];
  }

  /**
   * @prototype \PPHP\tools\patterns\metadata\reflection\Reflect
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
   * @prototype \PPHP\tools\patterns\metadata\reflection\Reflect
   */
  static public function getAllReflectionProperties(){
    $reflectionProperties = [];
    /**
     * @var Reflect $className
     */
    $className = get_called_class();
    /**
     * @var ReflectionClass $class
     */
    $class = $className::getReflectionClass();
    do{
      /**
       * @var ReflectionProperty[] $properties
       */
      $properties = $class->getProperties();
      foreach($properties as $property){
        if(array_key_exists($property->getName(), $reflectionProperties)){
          continue;
        }
        $className = $class->getName();
        $reflectionProperties[$property->getName()] = $className::getReflectionProperty($property->getName());
      }
      $class = $class->getParentClass();
    } while($class !== false);

    return $reflectionProperties;
  }

  /**
   * @prototype \PPHP\tools\patterns\metadata\reflection\Reflect
   */
  static public function getAllReflectionMethods(){
    $reflectionMethods = [];
    /**
     * @var Reflect $className
     */
    $className = get_called_class();
    /**
     * @var ReflectionClass $class
     */
    $class = $className::getReflectionClass();
    do{
      /**
       * @var ReflectionMethod[] $methods
       */
      $methods = $class->getMethods();
      foreach($methods as $method){
        if(array_key_exists($method->getName(), $reflectionMethods)){
          continue;
        }
        $className = $class->getName();
        $reflectionMethods[$method->getName()] = $className::getReflectionMethod($method->getName());;
      }
      $class = $class->getParentClass();
    } while($class !== false);

    return $reflectionMethods;
  }
}
