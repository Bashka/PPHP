<?php
namespace PPHP\tools\patterns\metadata\reflection;

/**
 * Класс, реализующий данный интерфейс, способен возвращать отображения своих членов.
 *
 * Данный интерфейс свидетельствует о возможности класса возвращать свое отображение, и отображения своих членов с устойчивым состоянием.
 * Возвращаемые отображения являются уникальными для каждого класса и члена, что означает использование одного объекта отражения для одного класса или члена.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\metadata\reflection
 */
interface Reflect{
  /**
   * Метод возвращает представление свойства класса.
   * @static
   * @abstract
   * @param string $propertyName Имя свойства.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return \PPHP\tools\patterns\metadata\reflection\ReflectionProperty Отображение свойства класса.
   */
  static public function &getReflectionProperty($propertyName);

  /**
   * Метод возвращает представление метода класса.
   * @static
   * @abstract
   * @param string $methodName Имя метода.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return \PPHP\tools\patterns\metadata\reflection\ReflectionMethod Отображение метода класса.
   */
  static public function &getReflectionMethod($methodName);

  /**
   * Метод возвращает представление класса.
   * @static
   * @abstract
   * @return \PPHP\tools\patterns\metadata\reflection\ReflectionClass Отображение класса.
   */
  static public function &getReflectionClass();

  /**
   * Метод возвращает представление родительского класса.
   * @static
   * @abstract
   * @return \PPHP\tools\patterns\metadata\reflection\ReflectionClass|null Отображение родительского класса или null - если данный класс является вершиной иерархии наследования.
   */
  static public function getParentReflectionClass();

  /**
   * Метод возвращает отображения всех свойств класса, в том числе видимых свойств родительского класса.
   * @static
   * @abstract
   * @return \SplObjectStorage Отображение всех свойств класса.
   */
  static public function getAllReflectionProperties();

  /**
   * Метод возвращает отображения всех методов класса, в том числе видимых методов родительского класса.
   * @static
   * @abstract
   * @return \SplObjectStorage Отображение всех методов класса.
   */
  static public function getAllReflectionMethods();
}
