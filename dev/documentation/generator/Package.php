<?php
namespace PPHP\dev\documentation\generator;

use PPHP\tools\classes\standard\fileSystem\ComponentFileSystem;
use PPHP\tools\classes\standard\fileSystem\Directory;
use PPHP\tools\patterns\interpreter\Interpreter;
use PPHP\tools\patterns\interpreter\Metamorphosis;
use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

class Package implements Interpreter, Metamorphosis{
  protected $name;

  protected $namespace;

  /**
   * @var Directory
   */
  protected $dir;

  /**
   * Метод восстанавливает объект из другого объекта.
   * @param Directory $object Исходный объект.
   * @param string $driver Полное имя родительского пакета.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае отсутствия требуемых для восстановления данных.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return static Результирующий объект.
   */
  public static function metamorphose($object, $driver = null){
    if(!($object instanceof Directory)){
      throw new exceptions\InvalidArgumentException('Недопустимое значение аргумента. Ожидается [Directory] вместо ['.gettype($object).']');
    }
    if(!$object->isExists()){
      throw new exceptions\NotFoundDataException('Отсутствует запрашиваемый пакет ['.$object->getAddress().'].');
    }
    return new Package($object, $object->getName(), $driver);
  }

  public function __construct($dir, $name, $namespace){
    exceptions\InvalidArgumentException::verifyType($name, 'S');
    $this->dir = $dir;
    $this->name = $name;
    $this->namespace = $namespace;
  }
  /**
   * Метод возвращает строку, полученную при интерпретации объекта.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты объекта.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver = null){
    $subNodes = '';
    /**
     * @var \DirectoryIterator $subPackage
     */
    foreach($this->dir->getDirectoryIterator() as $subPackage){
      if($subPackage != '.' && $subPackage != '..'){
        if($subPackage->isDir()){
          $subNodes .= Package::metamorphose($this->dir->getDir($subPackage->getFilename()), $this->namespace.'\\'.$this->name)->interpretation();
        }
        elseif(substr($subPackage->getFilename(), -4) == '.php'){
          $subNodes .= Element::metamorphose($this->dir->getFile($subPackage->getFilename()), $this->namespace.'\\'.$this->name)->interpretation();
        }
      }
    }
    if($this->dir->isFileExists('doc')){
      $docPackage = DocFile::reestablish($this->dir->getFile('doc')->getReader()->readAll(), $this->namespace.'\\'.$this->name)->interpretation();
    }
    else{
      $docPackage = '';
    }
    return '<node name="'.$this->name.'\\" prog_lang="custom-colors" readonly="False" tags="" unique_id="'.Generator::getUniqueId().'">'.$docPackage.$subNodes.'</node>';
  }
}