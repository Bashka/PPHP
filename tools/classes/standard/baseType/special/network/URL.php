<?php
namespace PPHP\tools\classes\standard\baseType\special\network;

/**
 * Класс-обертка служит для представления и верификации URL адреса.
 * Допустимый тип: совокупность следующих элементов: <протокол><IP|домен>[:<порт>][/адрес в файловой системе]
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\special\network
 */
class URL extends \PPHP\tools\classes\standard\baseType\wrapper{
  /**
   * Тип данной обертки.
   * @var string
   */
  protected static $type = 'URL';

  /**
   * Протокол.
   * @var \PPHP\tools\classes\standard\baseType\special\network\Report
   */
  protected $report;
  /**
   * Адрес ресурса.
   * @var \PPHP\tools\classes\standard\baseType\special\network\IPAddress|\PPHP\tools\classes\standard\baseType\special\network\DomainName
   */
  protected $address;
  /**
   * Порт.
   * @var \PPHP\tools\classes\standard\baseType\special\network\Port
   */
  protected $port;
  /**
   * Физический адрес ресурса.
   * @var \PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemAddress
   */
  protected $fileSystemAddress;

  /**
   * Метод приводит переданные данные к типу обертки.
   * @param mixed $val Приводимые данные.
   * @return mixed Приведенные данные.
   */
  protected function transform($val){
    $components = [];
    preg_match('/^([a-z0-9]+:\/\/)([a-z-.0-9]+)(:[0-9]{1,5})?(\/[^:*?"<>\|\0\\\]*)?$/i', (string)$val, $components);
    $this->report = new Report($components[1]);

    if(DomainName::is($components[2])){
      $this->address = new DomainName($components[2]);
    }
    else{
      $this->address = new IPAddress4($components[2]);
    }

    if(!empty($components[3])){
      $this->port = new Port(substr($components[3], 1));
    }

    if(!empty($components[4]) && $components[4] != '/'){
        $this->fileSystemAddress = new \PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemAddress($components[4]);
    }

    return (string) $val;
  }

  /**
   * Метод определяет, является ли указанное значение допустимым типом.
   * @static
   * @param mixed $val Проверяемое значение.
   * @return boolean true - если данные являются допустимым типом или могут быть приведены к нему без потери данных, иначе - false.
   */
  public static function is($val){
    $components = [];
    preg_match('/^([a-z0-9]+:\/\/)([a-z-.0-9]+)(:[0-9]{1,5})?(\/[^:*?"<>\|\0\\\]*)?$/i', (string)$val, $components);

    // Протокол
    if(!isset($components[1]) || !Report::is($components[1])){
      return false;
    }
    // Домен/IP
    if(!isset($components[2]) || (!DomainName::is($components[2]) && !IPAddress4::is($components[2]))){
      return false;
    }
    // Порт
    if(!empty($components[3])){
      if(!Port::is(substr($components[3], 1))){
        return false;
      }
    }
    // Адрес ресурса
    if(!empty($components[4]) && $components[4] != '/'){
      if(!\PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemAddress::is($components[4])){
        return false;
      }
    }
    return true;
  }

  /**
   * @return \PPHP\tools\classes\standard\baseType\special\network\DomainName|\PPHP\tools\classes\standard\baseType\special\network\IPAddress
   */
  public function getAddress(){
    return $this->address;
  }

  /**
   * @return \PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemAddress|null
   */
  public function getFileSystemAddress(){
    return $this->fileSystemAddress;
  }

  /**
   * @return \PPHP\tools\classes\standard\baseType\special\network\Port|null
   */
  public function getPort(){
    return $this->port;
  }

  /**
   * @return \PPHP\tools\classes\standard\baseType\special\network\Report
   */
  public function getReport(){
    return $this->report;
  }
}
