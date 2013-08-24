<?php
namespace PPHP\tools\classes\standard\baseType;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс-обертка служит для верификации и представления логических данных в системе.
 * Истинной здесь являются следующие данные: логическое true; строка "true".
 * Ложью здесь являются следующие данные: логическое false; строка "false".
 * Другие данные приводят к выбросу исключения exceptions\InvalidArgumentException.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType
 */
class Boolean extends Wrapper{
  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return ['((?:true)|(?:false))'];
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
    if($string == 'true'){
      return new self(true);
    }
    else{
      return new self(false);
    }
  }

  function __construct($val){
    if(!is_bool($val)){
      throw exceptions\InvalidArgumentException::getTypeException('boolean', gettype($val));
    }
    parent::__construct($val);
  }
}
