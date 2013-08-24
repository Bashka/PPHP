<?php
namespace PPHP\tools\classes\standard\baseType\special\network;

use \PPHP\tools\classes\standard\baseType as baseType;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс-обертка служит для представления и верификации адресов электронной почты.
 * Допустимый тип: только латинские буквы, цифры, знак подчеркивания и тире, за которым следует знак @ за которым следует доменное имя.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\special\network
 */
class EMail extends baseType\Wrapper{
  /**
   * Локальное имя пользователя электронной почты.
   * @var string
   */
  protected $local;

  /**
   * Домен электронной почты.
   * @var DomainName
   */
  protected $domain;

  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return ['([A-Za-z0-9_-]+)@(' . DomainName::getMasks()[0] . ')'];
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
    $o->local = $m[1];
    $o->domain = DomainName::reestablish($m[2]);

    return $o;
  }

  /**
   * @return DomainName
   */
  public function getDomain(){
    return $this->domain;
  }

  /**
   * @return string
   */
  public function getLocal(){
    return $this->local;
  }
}
