<?php
namespace PPHP\tools\classes\standard\baseType\special\network;

use \PPHP\tools\classes\standard\baseType as baseType;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс-обертка служит для представления и верификации доменных имен.
 * Допустимый тип: должно начинаться латинской буквой или цифрой, а заканчиваться буквой, цифрой или знаком тире. Может содержать точки, но не идущие подряд и обязательно обрамленые знаком тире, латинской буквой или цифрой.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\special\network
 */
class DomainName extends baseType\Wrapper{
  /**
   * Компоненты адреса.
   * @var string[]
   */
  protected $subDomains = [];

  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return ['[A-Za-z0-9][A-Za-z0-9-]*(?:(?:\.[A-Za-z0-9-]+)*|\.)[A-Za-z0-9]'];
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
    parent::reestablish($string);
    $o = new self($string);
    $o->subDomains = array_reverse(explode('.', $string));

    return $o;
  }

  /**
   * Метод возвращает указанное значение компонента адреса.
   * @param integer $index Индекс компонента в диапазоне от 0 до порядкового номера поддомена.
   * @return string Значение компонента адреса.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае передаче параметра недопустимого типа.
   */
  public function getComponent($index){
    exceptions\InvalidArgumentException::verifyType($index, 'i');
    exceptions\InvalidArgumentException::verifyVal($index, 'i [] 0 ' . count($this->subDomains));

    return $this->subDomains[$index];
  }
}
