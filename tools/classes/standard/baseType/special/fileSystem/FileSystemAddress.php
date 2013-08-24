<?php
namespace PPHP\tools\classes\standard\baseType\special\fileSystem;

use \PPHP\tools\classes\standard\baseType as baseType;
use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс-обертка служит для представления и верификации адресов файлов и папок в файловой системе.
 * Допустимый тип: любые символы кроме  : * ? " < > | \0 \ и без ведущего / символа, а так же без двух и более / символов, следующих один за другим.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\special\fileSystem
 */
class FileSystemAddress extends baseType\Wrapper{
  /**
   * Флаг абсолютного адреса.
   * @var boolean true - если адрес абсолютный, иначе - false.
   */
  protected $isRoot;

  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return ['(\/)?(?:' . FileSystemName::getPatterns()['fieldName'] . ')(?:\/' . FileSystemName::getPatterns()['fieldName'] . ')*(?:\/)?'];
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
    if(isset($m[1])){
      $o->isRoot = true;
    }
    else{
      $o->isRoot = false;
    }

    return $o;
  }

  /**
   * Метод определяет, является адрес абсолютным или относительным.
   * @return boolean true - если адрес абсолютный, иначе - false.
   */
  public function isRoot(){
    return $this->isRoot;
  }
}
