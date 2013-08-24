<?php
namespace PPHP\tools\classes\standard\baseType\special\network;

use \PPHP\tools\classes\standard\baseType as baseType;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс-обертка служит для представления и верификации URL адреса.
 * Допустимый тип: совокупность следующих элементов: <протокол><IP|домен>[:<порт>][/адрес в файловой системе]
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\special\network
 */
class URL extends baseType\Wrapper{
  /**
   * Протокол.
   * @var Report
   */
  protected $report;

  /**
   * Адрес ресурса.
   * @var IPAddress|DomainName
   */
  protected $address;

  /**
   * Порт.
   * @var Port
   */
  protected $port;

  /**
   * Физический адрес ресурса.
   * @var baseType\special\fileSystem\FileSystemAddress
   */
  protected $fileSystemAddress;

  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return [Report::getMasks()[0] . '((?:' . DomainName::getMasks()[0] . ')|(?:' . IPAddress4::getMasks()[0] . '))(?::(' . Port::getMasks()[0] . '))?(' . baseType\special\fileSystem\FileSystemAddress::getMasks()[0] . ')?'];
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
    $o->report = Report::reestablish($m[1] . '://');
    if(DomainName::isReestablish($m[2])){
      $o->address = DomainName::reestablish($m[2]);
    }
    else{
      $o->address = IPAddress4::reestablish($m[2]);
    }
    $o->port = Port::reestablish($m[7]);
    $o->fileSystemAddress = baseType\special\fileSystem\FileSystemAddress::reestablish($m[8]);

    return $o;
  }

  /**
   * @return DomainName|IPAddress
   */
  public function getAddress(){
    return $this->address;
  }

  /**
   * @return baseType\special\fileSystem\FileSystemAddress|null
   */
  public function getFileSystemAddress(){
    return $this->fileSystemAddress;
  }

  /**
   * @return Port|null
   */
  public function getPort(){
    return $this->port;
  }

  /**
   * @return Report
   */
  public function getReport(){
    return $this->report;
  }
}
