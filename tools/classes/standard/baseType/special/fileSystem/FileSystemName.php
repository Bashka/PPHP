<?php
namespace PPHP\tools\classes\standard\baseType\special\fileSystem;

use \PPHP\tools\classes\standard\baseType as baseType;
use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс-обертка служит для представления и верификации имен файлов и папок.
 * Допустимый тип: любые символы кроме / : * ? " < > | \0 \
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\special\fileSystem
 */
class FileSystemName extends baseType\Wrapper{
  /**
   * Имя ресурса файловой системы.
   * @var string
   */
  protected $name;

  /**
   * Расширение ресурса файловой системы.
   * @var string
   */
  protected $expansion;

  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return [FileSystemName::getPatterns()['fieldName']];
  }

  /**
   * Метод должен возвращать массив шаблонов, описывающих различные компоненты шаблонов верификации.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getPatterns($driver = null){
    return ['fieldName' => '[^\/:*?"<>\|\0\\\]+'];
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
    $strVal = new baseType\String($string);
    $positionPoint = $strVal->search('.', false, false);
    if($positionPoint > -1){
      $o->name = $strVal->subLeft($positionPoint)->getVal();
      $o->expansion = $strVal->subRight($strVal->length() - 2 - $positionPoint)->getVal();
    }
    else{
      $o->name = $string;
    }

    return $o;
  }

  /**
   * @return string
   */
  public function getExpansion(){
    return $this->expansion;
  }

  /**
   * @return string
   */
  public function getName(){
    return $this->name;
  }
}
