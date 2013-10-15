<?php
namespace PPHP\tools\patterns\metadata\reflection;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс, реализующий данный интерфейс, способен возвращать отражение себя и своих членов с устойчивыми состояниями и возможностью аннотирования.
 * Возвращаемые отражения являются неповторимыми для каждого класса и члена, это означает, что при повторном запросе отражения класса или члена, возвращается запрошенное ранее отражение.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\metadata\reflection
 */
interface Reflect{
  /**
   * Метод возвращает отражение свойства вызываемого класса в том числе, если свойство относится к родительскому классу.
   * @static
   * @abstract
   * @param string $name Имя свойства.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\ComponentClassException Выбрасывается при запросе отражения не определенного члена.
   * @return ReflectionProperty Отражение свойства класса.
   */
  static public function &getReflectionProperty($name);

  /**
   * Метод возвращает отражение метода вызываемого класса в том числе, если метод относится к родительскому классу.
   * @static
   * @abstract
   * @param string $name Имя метода.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\ComponentClassException Выбрасывается при запросе отражения не определенного члена.
   * @return ReflectionMethod Отражение метода класса.
   */
  static public function &getReflectionMethod($name);

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
