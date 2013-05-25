<?php
namespace PPHP\tools\classes\standard\baseType;
use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс-обертка служит для предоставления дополнительной логики календарным числам.
 * Допустимый тип: строка вида d.m.y|Y
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType
 */
class Date extends Wrapper{
  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return [
      '(?:(?:[1-9])|(?:[1-2][0-9])|(?:3[0-1]))\.(?:(?:[1-9])|(?:1[0-2]))\.[0-9]+'
    ];
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

    return new self(\DateTime::createFromFormat('d.m.Y', $string));
  }

  function __construct($val){
    if(!is_a($val, '\DateTime')){
      throw exceptions\InvalidArgumentException::getTypeException('DateTime', gettype($val));
    }
    parent::__construct($val);
  }

  function __toString(){
    return (string) $this->val->format('d.m.Y');
  }
}
