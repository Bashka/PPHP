<?php
namespace PPHP\tools\classes\standard\baseType\special\network;

use \PPHP\tools\classes\standard\baseType as baseType;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс-обертка служит для представления и верификации имен протоколов обмена данными.
 * Допустимый тип: имя одного из доступных протоколов за которым следует последовательность ://
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\special\network
 */
class Report extends baseType\Wrapper{
  const HTTP = 'http';

  const HTTPS = 'https';

  const FTP = 'ftp';

  const DNS = 'dns';

  const SSH = 'ssh';

  const POP3 = 'pop3';

  const SMTP = 'smtp';

  /**
   * Имя протокола.
   * @var string
   */
  protected $name;

  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return ['(' . self::HTTP . '|' . self::HTTPS . '|' . self::FTP . '|' . self::DNS . '|' . self::SSH . '|' . self::POP3 . '|' . self::SMTP . '):\/\/'];
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
    $o->name = $m[1];

    return $o;
  }

  public function getName(){
    return $this->name;
  }
}
