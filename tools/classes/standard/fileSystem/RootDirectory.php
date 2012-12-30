<?php
namespace PPHP\tools\classes\standard\fileSystem;

/**
 * Класс представляет корневой каталог файловой системы.
 */
class RootDirectory extends Directory{
  /**
   * Конструктор переопределяет родительский и не требует указания имени и родительского каталога для корневого каталога. Вместо этого, в качестве имени используется пустая строка, а в качестве родительского каталога сам корневой каталог системы.
   */
  public function __construct(){
    parent::__construct('', $this);
  }

  /**
   * Метод всегда возвращает домен ресурса.
   * @return string
   */
  public function getLocationAddress(){
    return $_SERVER['DOCUMENT_ROOT'];
  }

  /**
   * Метод всегда возвращает домен ресурса.
   * @return string
   */
  public function getAddress(){
    return $this->getLocationAddress();
  }

  /**
   * Метод запрещен в данном классе и генерирует исключение при обращении к нему.
   * @param string $newName
   * @throws UpdatingRoodException
   */
  public function rename($newName){
    throw new UpdatingRoodException('Невозможно модифицировать корневой каталог.');
  }

  /**
   * Метод запрещен в данном классе и генерирует исключение при обращении к нему.
   * @param Directory $location
   * @throws UpdatingRoodException
   */
  public function move(Directory $location){
    throw new UpdatingRoodException('Невозможно модифицировать корневой каталог.');
  }

  /**
   * Метод всегда возвращает true.
   * @return bool
   */
  public function isExists(){
    return true;
  }

  /**
   * Метод запрещен в данном классе и генерирует исключение при обращении к нему.
   * @param Directory $location
   * @throws UpdatingRoodException
   */
  public function copyPaste(Directory $location){
    throw new UpdatingRoodException('Невозможно модифицировать корневой каталог.');
  }

  /**
   * Метод всегда возвращает 0.
   * @return int
   */
  public function getSize(){
    return 0;
  }

  /**
   * Метод запрещен в данном классе и генерирует исключение при обращении к нему.
   * @param int $mode
   * @throws UpdatingRoodException
   */
  public function create($mode = 0777){
    throw new UpdatingRoodException('Невозможно модифицировать корневой каталог.');
  }
}
