<?php
namespace PPHP\tools\patterns\metadata\reflection;

/**
 * Класс, реализующий данный интерфейс, способен возвращать отображения своих членов.
 */
interface Reflect{
  /**
   * Метод возвращает представление свойства класса.
   * @static
   * @abstract
   * @param string $propertyName Имя свойства.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве аргумента передан неверный тип, пустая строка или данного члена класса не существует.
   * @return \PPHP\tools\patterns\metadata\reflection\ReflectionProperty
   */
  static public function &getReflectionProperty($propertyName);

  /**
   * Метод возвращает представление метода класса.
   * @static
   * @abstract
   * @param string $methodName Имя метода.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве аргумента передан неверный тип, пустая строка или данного члена класса не существует.
   * @return \PPHP\tools\patterns\metadata\reflection\ReflectionMethod
   */
  static public function &getReflectionMethod($methodName);

  /**
   * Метод возвращает представление класса.
   * @static
   * @abstract
   * @return \PPHP\tools\patterns\metadata\reflection\ReflectionClass
   */
  static public function &getReflectionClass();

  /**
   * Метод возвращает представление родительского класса.
   * @static
   * @abstract
   * @return \PPHP\tools\patterns\metadata\reflection\ReflectionClass|null Представление родительского класса или null - если данный класс является вершиной иерархии наследования.
   */
  static public function getParentReflectionClass();

  /**
   * Метод возвращает отображения всех свойств класса, в том числе видимые свойства родительского класса.
   * @static
   * @abstract
   * @return \ArrayAccess
   */
  static public function getAllReflectionProperties();

  /**
   * Метод возвращает отображения всех методов класса, в том числе видимые методы родительского класса.
   * @static
   * @abstract
   * @return \ArrayAccess
   */
  static public function getAllReflectionMethods();
}
