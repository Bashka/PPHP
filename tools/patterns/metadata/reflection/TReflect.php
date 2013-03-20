<?php
namespace PPHP\tools\patterns\metadata\reflection;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Классическая реализация интерфейса Reflect.
 *
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
   * Метод возвращает отражение свойства вызываемого класса.
   * @static
   *
   * @param string $propertyName Имя свойства.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return ReflectionProperty Отражение свойства класса.
   */
  static public function &getReflectionProperty($propertyName){
    if(!is_string($propertyName)){
      throw new exceptions\InvalidArgumentException('string', $propertyName);
    }
    elseif(empty($propertyName) || !property_exists(get_called_class(), $propertyName)){
      throw new exceptions\InvalidArgumentException('Указанное свойства ' . $propertyName . ' отсутствует в вызываемом классе.');
    }

    $reflectionProperty = new ReflectionProperty(get_called_class(), $propertyName);

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
   * Метод возвращает отражение метода вызываемого класса.
   * @static
   *
   * @param string $methodName Имя метода.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return ReflectionMethod Отражение метода класса.
   */
  static public function &getReflectionMethod($methodName){
    if(!is_string($methodName)){
      throw new exceptions\InvalidArgumentException('string', $methodName);
    }
    elseif(empty($methodName) || !method_exists(get_called_class(), $methodName)){
      throw new exceptions\InvalidArgumentException('Указанный метод ' . $methodName . ' отсутствует в вызываемом классе.');
    }

    $reflectionMethod = new ReflectionMethod(get_called_class(), $methodName);

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
   * Метод возвращает отражения всех свойств вызываемого класса, в том числе видимых свойств родительского класса.
   * Следует учитывать, что данная реализация не позволяет получить private члены родительских классов. Для решения этой проблемы достаточно вызвать переопределеный метод в родительском классе и сконкатенировать результат переопределяющего метода.
   *
   * @static
   * @return \SplObjectStorage Отражение всех свойств класса.
   */
  static public function getAllReflectionProperties(){
    $reflectionProperties = new \SplObjectStorage();
    $namesAllProperties = static::getReflectionClass()->getProperties();
    foreach($namesAllProperties as $v){
      try{
        $reflectionProperties->attach(static::getReflectionProperty($v->getName()));
      }
      catch(exceptions\InvalidArgumentException $exc){
      }
    }
    return $reflectionProperties;
  }

  /**
   * Метод возвращает отражения всех методов вызываемого класса, в том числе видимых методов родительского класса.
   * Следует учитывать, что данная реализация не позволяет получить private члены родительских классов. Для решения этой проблемы достаточно вызвать переопределеный метод в родительском классе и сконкатенировать результат переопределяющего метода.
   *
   * @static
   * @return \SplObjectStorage Отражение всех методов класса.
   */
  static public function getAllReflectionMethods(){
    $reflectionMethods = new \SplObjectStorage();
    $namesAllMethods = static::getReflectionClass()->getMethods();
    foreach($namesAllMethods as $v){
      try{
        $reflectionMethods->attach(self::getReflectionMethod($v->getName()));
      }
      catch(exceptions\InvalidArgumentException $exc){
      }
    }
    return $reflectionMethods;
  }
}
