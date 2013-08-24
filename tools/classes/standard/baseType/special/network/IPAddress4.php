<?php
namespace PPHP\tools\classes\standard\baseType\special\network;

use \PPHP\tools\classes\standard\baseType as baseType;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс-обертка служит для представления и верификации IP-адреса 4 версии.
 * Допустимый тип: четыре цифры в диапазоне от 0 до 255 идущие подряд, разделеные точками.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\special\network
 */
class IPAddress4 extends baseType\Wrapper implements IPAddress{
  /**
   * Компоненты адреса.
   * @var integer[]
   */
  protected $trio = [0, 0, 0, 0];

  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return ['(' . self::getPatterns()['component'] . ')\.(' . self::getPatterns()['component'] . ')\.(' . self::getPatterns()['component'] . ')\.(' . self::getPatterns()['component'] . ')'];
  }

  /**
   * Метод должен возвращать массив шаблонов, описывающих различные компоненты шаблонов верификации.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getPatterns($driver = null){
    return ['component' => '(?:[0-9])|(?:[1-9][0-9])|(?:1[0-9][0-9])|(?:2[0-5][0-5])'];
  }

  /**
   * Метод восстанавливает объект из строки.
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws exceptions\StructureException Выбрасывается в случае, если исходная строка не отвечает требования структуры.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return static Результирующий объект.
   */
  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    $m = parent::reestablish($string);
    $o = new self($string);
    $o->trio[0] = $m[1];
    $o->trio[1] = $m[2];
    $o->trio[2] = $m[3];
    $o->trio[3] = $m[4];

    return $o;
  }

  /**
   * Метод возвращает указанное значение компонента адреса.
   * @param integer $index Индекс компонента в диапазоне от 0 до 3.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае передаче параметра недопустимого типа.
   * @return integer Значение компонента адреса.
   */
  public function getTrio($index){
    exceptions\InvalidArgumentException::verifyType($index, 'i');
    exceptions\InvalidArgumentException::verifyVal($index, 'i [] 0 3');

    return $this->trio[$index];
  }

  /**
   * Метод возвращает значение компонента адреса в двоичной форме.
   * @param integer $index Индекс компонента в диапазоне от 0 до 3.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае передаче параметра недопустимого типа.
   * @return string Значение компонента адреса в двоичной форме.
   */
  public function getTrioBin($index){
    return decbin($this->getTrio($index));
  }
}
