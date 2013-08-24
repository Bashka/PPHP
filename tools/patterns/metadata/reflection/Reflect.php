<?php
namespace PPHP\tools\patterns\metadata\reflection;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс, реализующий данный интерфейс, способен возвращать отображения своих членов.
 * Данный интерфейс свидетельствует о возможности класса возвращать свое отражение, и отображения своих членов с устойчивым состоянием.
 * Возвращаемые отображения являются уникальными для каждого класса и члена, что означает использование одного объекта отражения для одного класса или члена.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\metadata\reflection
 */
interface Reflect{
  /**
   * Метод возвращает отражение свойства вызываемого класса в том числе, если свойство относится к родительскому классу.
   * @static
   * @abstract
   * @param string $propertyName Имя свойства.
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws exceptions\ComponentClassException Выбрасывается при запросе отражения не определенного члена.
   * @return ReflectionProperty Отражение свойства класса.
   */
  static public function &getReflectionProperty($propertyName);

  /**
   * Метод возвращает отражение метода вызываемого класса в том числе, если метод относится к родительскому классу.
   * @static
   * @abstract
   * @param string $methodName Имя метода.
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws exceptions\ComponentClassException Выбрасывается при запросе отражения не определенного члена.
   * @return ReflectionMethod Отражение метода класса.
   */
  static public function &getReflectionMethod($methodName);

  /**
   * Метод возвращает отражение вызываемого класса.
   * @static
   * @abstract
   * @return ReflectionClass Отражение класса.
   */
  static public function &getReflectionClass();

  /**
   * Метод возвращает отражение родительского класса.
   * @static
   * @abstract
   * @return ReflectionClass|null Отражение родительского класса или null - если данный класс является вершиной иерархии наследования.
   */
  static public function getParentReflectionClass();

  /**
   * Метод возвращает отражения всех свойств вызываемого класса и его родителей.
   * @static
   * @abstract
   * @return ReflectionProperty[] Отражение всех свойств класса в виде ассоциативного массива, ключами которого являются имена, а значениями отражения свойств класса.
   */
  static public function getAllReflectionProperties();

  /**
   * Метод возвращает отражения всех методов вызываемого класса и его родителей.
   * @static
   * @abstract
   * @return ReflectionMethod[] Отражение всех методов класса в виде ассоциативного массива, ключами которого являются имена, а значениями отражения методов класса
   */
  static public function getAllReflectionMethods();
}
