<?php
namespace PPHP\tools\patterns\interpreter;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Реализация интерфейса Restorable по средствам шаблонов и их сочетаний.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\interpreter
 */
trait TRestorable{
  /**
   * Метод должен возвращать массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return [];
  }

  /**
   * Метод должен возвращать массив шаблонов, описывающих различные компоненты шаблонов верификации.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getPatterns($driver = null){
    return [];
  }

  /**
   * Метод последовательно применяет доступные шаблоны к строке с целью определения шаблона, которому соответствует строка.
   * @param string $string Проверяемая строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[] Массив, содержащий в числовых ключах компоненты, выделенные в шаблоне верификации, а в ключе key ключ ассоциативного массива, которому соответствует первый подходящий шаблон.
   * Пустая строка если строка не соответствует ни одному шаблону.
   */
  private static function searchMask($string, $driver = null){
    static::updateString($string);
    foreach(static::getMasks($driver) as $key => $mask){
      $matches = [];
      if(preg_match('/^'.$mask.'$/u', $string, $matches)){
        $matches['key'] = $key;
        return $matches;
      }
    }
    return '';
  }

  /**
   * Метод позволяет определить допустимость интерпретации исходной строки в объект.
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return boolean true - если интерпретация возможна, иначе - false.
   */
  public static function isReestablish($string, $driver = null){
    exceptions\InvalidArgumentException::verifyType($string, 's');
    if(static::searchMask($string, $driver) === ''){
      return false;
    }
    return true;
  }

  /**
   * Данный метод выполняет стандартную проверку строки на предмет возможности восстановления.
   * Метод должен быть переопределен в дочерних классах с обязательным вызовом его в данном классе.
   * Возвращаемое значение метода может быть использовано в дочерних классах для выбора механизма интерпретации.
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws exceptions\StructureException Выбрасывается в случае, если исходная строка не отвечает требования структуры.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Ключ ассоциативного массива, которому соответствует первый подходящий шаблон или пустая строка если строка не соответствует ни одному шаблону.
   */
  public static function reestablish($string, $driver = null){
    exceptions\InvalidArgumentException::verifyType($string, 'S');
    $mask = static::searchMask($string, $driver);
    if($mask === ''){
      throw new exceptions\StructureException('Недопустимая структура для объекта '.get_called_class().' ['.$string.'].');
    }
    return $mask;
  }

  /**
   * Данный метод вызывается автоматически и служит для приведения исходной строки к некоторому формату.
   * Использующие данный trait классы могут переопределять данный метод.
   * Данный метод вызывается от имени используемого класса.
   * @param string $string Исходная строка.
   */
  public static function updateString(&$string){}
}