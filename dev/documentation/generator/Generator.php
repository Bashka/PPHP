<?php
namespace PPHP\dev\documentation\generator;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\classes\standard\fileSystem\ComponentFileSystem;
use PPHP\tools\patterns\interpreter\Interpreter;

class Generator implements Interpreter{
  private static $uniqueId = 1;

  protected $packages = ['tools'];

  public static function getUniqueId(){
    return self::$uniqueId++;
  }

  public function __construct(array $packages = null){
    if(!is_null($packages)){
      $this->packages = $packages;
    }
  }

  /**
   * Метод возвращает строку, полученную при интерпретации объекта.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты объекта.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver = null){
    $packageDoc = '';
    foreach($this->packages as $package){
      $packageAddress = $_SERVER['DOCUMENT_ROOT'].'/PPHP/'.$package;
      $packageDoc .= Package::metamorphose(ComponentFileSystem::constructDirFromAddress($packageAddress), '\PPHP')->interpretation();
    }
    return '<cherrytree>'.$packageDoc.'</cherrytree>';
  }
}