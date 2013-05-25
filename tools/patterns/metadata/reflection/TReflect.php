<?php
namespace PPHP\tools\patterns\metadata\reflection;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Классическая реализация интерфейса Reflect.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\metadata\reflection
 */
trait TReflect{
  /**
   * Отражение класса.
   * Свойство включает отражения классов во всей иерархии наследования данного класса.
   * В качестве ключа массива используется имя класса, а в качестве значения, его отражение.
   * @var ReflectionClass[]
   */
  static protected $reflectionClass = [];
  /**
   * Множество отражений свойств класса.
   * Свойство включает отражения свойств классов во всей иерархии наследования данного класса.
   * В качестве ключа массива используется имя класса, в котором декларировано свойство, а в качестве значения, его отражение.
   * @var ReflectionProperty[][]
   */
  static protected $reflectionProperties = [];
  /**
   * Множество отражений методов класса.
   * Свойство включает отражения методов классов во всей иерархии наследования данного класса.
   * В качестве ключа массива используется имя класса, в котором декларирован метод, а в качестве значения, его отражение.
   * @var ReflectionMethod[][]
   */
  static protected $reflectionMethods = [];

  /**
   * Метод возвращает отражение свойства вызываемого класса в том числе, если свойство относится к родительскому классу.
   * @static
   *
   * @param string $propertyName Имя свойства.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws exceptions\ComponentClassException Выбрасывается при запросе отражения не определенного члена.
   * @return ReflectionProperty Отражение свойства класса.
   */
  static public function &getReflectionProperty($propertyName){
    exceptions\InvalidArgumentException::verifyType($propertyName, 'S');

    $class = get_called_class();
    while(!property_exists($class, $propertyName)){
      $parentClass = $class::getReflectionClass()->getParentClass();
      if($parentClass === false){
        throw new exceptions\ComponentClassException('Указанное свойство [' . $propertyName . '] отсутствует в вызываемом классе и его надклассах.');
      }
      $class = $parentClass->getName();
    }
    $reflectionProperty = new ReflectionProperty($class, $propertyName);

    // Проверка отношения получаемого отражения к классам в иерархии наследования
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
   * Метод возвращает отражение метода вызываемого класса в том числе, если метод относится к родительскому классу.
   * @static
   *
   * @param string $methodName Имя метода.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws exceptions\ComponentClassException Выбрасывается при запросе отражения не определенного члена.
   * @return ReflectionMethod Отражение метода класса.
   */
  static public function &getReflectionMethod($methodName){
    exceptions\InvalidArgumentException::verifyType($methodName, 'S');

    $class = get_called_class();
    while(!method_exists($class, $methodName)){
      $parentClass = $class::getReflectionClass()->getParentClass();
      if($parentClass === false){
        throw new exceptions\ComponentClassException('Указанный метод [' . $methodName . '] отсутствует в вызываемом классе и его надклассах.');
      }
      $class = $parentClass->getName();
    }
    $reflectionMethod = new ReflectionMethod($class, $methodName);

    // Проверка отношения получаемого отражения к классам в иерархии наследования
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
   * Метод возвращает отражение вызываемого класса.
   * @static
   * @return ReflectionClass Отражение класса.
   */
  static public function &getReflectionClass(){
    if(!isset(self::$reflectionClass[get_called_class()])){
      self::$reflectionClass[get_called_class()] = new ReflectionClass(get_called_class());
    }
    return static::$reflectionClass[get_called_class()];
  }

  /**
   * Метод возвращает отражение родительского класса.
   * @static
   * @return ReflectionClass|null Отражение родительского класса или null - если данный класс является вершиной иерархии наследования.
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
   * Метод возвращает отражения всех свойств вызываемого класса и его родителей.
   *
   * @static
   * @return ReflectionProperty[] Отражение всех свойств класса в виде ассоциативного массива, ключами которого являются имена, а значениями отражения свойств класса.
   */
  static public function getAllReflectionProperties(){
    $reflectionProperties = [];
    $class = get_called_class();
    $class = $class::getReflectionClass();
    do{
      $properties = $class->getProperties();
      foreach($properties as $property){
        if(array_key_exists($property->getName(), $reflectionProperties)){
          continue;
        }
        $className = $class->getName();
        $reflectionProperties[$property->getName()] = $className::getReflectionProperty($property->getName());
      }
      $class = $class->getParentClass();
    }while($class !== false);
    return $reflectionProperties;
  }

  /**
   * Метод возвращает отражения всех методов вызываемого класса и его родителей.
   *
   * @static
   * @return ReflectionMethod[] Отражение всех методов класса в виде ассоциативного массива, ключами которого являются имена, а значениями отражения методов класса
   */
  static public function getAllReflectionMethods(){
    $reflectionMethods = [];
    $class = get_called_class();
    $class = $class::getReflectionClass();
    do{
      $methods = $class->getMethods();
      foreach($methods as $method){
        if(array_key_exists($method->getName(), $reflectionMethods)){
          continue;
        }
        $className = $class->getName();
        $reflectionMethods[$method->getName()] = $className::getReflectionMethod($method->getName());;
      }
      $class = $class->getParentClass();
    }while($class !== false);
    return $reflectionMethods;
  }
}
