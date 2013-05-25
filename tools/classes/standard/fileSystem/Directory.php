<?php
namespace PPHP\tools\classes\standard\fileSystem;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет каталог файловой системы в программе и предоставляет механизмы доступа к его содержимому.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\fileSystem
 */
class Directory extends ComponentFileSystem{
  /**
   * Метод изменяет имя компонента на заданное, если это возможно.
   *
   * @param string $newName Новое имя компонента.
   *
   * @throws exceptions\DuplicationException Выбрасывается в случае, если переименование компонента приведет к дублированию.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return boolean true - в случае успешного завершения операции, иначе - false.
   */
  public function rename($newName){
    if($this->getLocation()->isDirExists($newName)){
      throw new exceptions\DuplicationException('Невозможно выполнить действие. Компонент с данным именем уже существует.');
    }
    return parent::rename($newName);
  }

  /**
   * Метод перемещает компонент в данный каталог.
   *
   * @param Directory $location Целевой каталог.
   *
   * @throws exceptions\DuplicationException Выбрасывается в случае, если целевой каталог уже содержит компонент с тем же именем, что и перемещаемый.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @throws exceptions\RuntimeException Выбрасывается в случае нарушения логики работы файловой системы путем перемещения компонента в себя.
   * @return boolean true - в случае успешного завершения операции, иначе - false.
   */
  public function move(Directory $location){
    if($location->isDirExists($this->getName())){
      throw new exceptions\DuplicationException('Невозможно выполнить действие. Компонент с данным именем уже существует.');
    }
    if($location->getAddress() == $this->getAddress()){
      throw new exceptions\RuntimeException('Ожидается отличный от данного каталога каталог.');
    }
    return parent::move($location);
  }

  /**
   * Метод определяет, существует ли вызывающий компонент на момент вызова метода.
   * @throws NotExistsException Выбрасывается в случае, если родительского каталога не существует.
   * @return boolean true - если компонент на момент вызова метода существует в файловой системе, иначе - false.
   */
  public function isExists(){
    try{
      return $this->getLocation()->isDirExists($this->getName());
    }
    catch(NotExistsException $exception){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе родительский каталог не найден.', 0, $exception);
    }
  }

  /**
   * Метод копирует компонента в данный каталог.
   *
   * @param Directory $location Целевой каталог.
   *
   * @throws exceptions\DuplicationException Выбрасывается в случае, если целевой каталог уже содержит копируемый компонент.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return boolean true - в случае успешного завершения операции, иначе - false.
   */
  public function copyPaste(Directory $location){
    // Проверка на выбрасывание исключений используемыми методами не выполняется из за рекурсивности
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    if($location->isDirExists($this->getName())){
      throw new exceptions\DuplicationException('Невозможно выполнить действие. Компонент с данным именем уже существует.');
    }

    $copyDirRoot = new Directory($this->getName(), $location);
    try{
      if(!$copyDirRoot->create()){
        return false;
      }
    }
    catch(exceptions\DuplicationException $e){
      throw $e;
    }
    // Дальнейшая проверка не выполняется в связи с наличием проверки возможных исключений в начале метода

    $iterator = $this->getDirectoryIterator();
    foreach($iterator as $component){
      if($component == '.' || $component == '..'){
        continue;
      }
      // Перехват исключений не выполняется в связи невозможностью их появления
      if($component->isDir()){
        $this->getDir((string) $component)->copyPaste($copyDirRoot);
      }
      elseif($component->isFile()){
        $this->getFile((string) $component)->copyPaste($copyDirRoot);
      }
    }
    return true;
  }

  /**
   * Метод удаляет текущий компонент из файловой системы.
   * @abstract
   * @throws NotExistsException Выбрасывается в случае, если удаляемого компонента не существует.
   * @return boolean true - в случае успешного завершения операции, иначе - false.
   */
  public function delete(){
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    $this->clear(); // Перехват исключений не выполняется в связи с невозможностью их появления
    return rmdir($this->getAddress());
  }

  /**
   * Метод отчищает директорую от содержимого.
   * @return boolean true - если метод выполнен успешно, иначе - false.
   * @throws NotExistsException Выбрасывается в случае, если удаляемого компонента не существует.
   */
  public function clear(){
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    $iterator = $this->getDirectoryIterator();
    foreach($iterator as $component){
      if($component == '.' || $component == '..'){
        continue;
      }

      // Перехват исключений не выполняется в связи с невозможностью их появления и возможной рекурсией с методом delete
      if($component->isDir()){
        $this->getDir((string) $component)->delete();
      }
      elseif($component->isFile()){
        $this->getFile((string) $component)->delete();
      }
    }
    return true;
  }

  /**
   * Метод возвращает размер в байтах данного компонента.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return integer Размер компонента в байтах.
   */
  public function getSize(){
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    $iterator = $this->getDirectoryIterator();
    $size = 0;
    foreach($iterator as $component){
      if($component == '.' || $component == '..'){
        continue;
      }

      // Перехват исключений не выполняется в связи с невозможностью их появления
      if($component->isDir()){
        $size += $this->getDir((string) $component)->getSize();
      }
      elseif($component->isFile()){
        $size += $this->getFile((string) $component)->getSize();
      }
    }
    return $size;
  }

  /**
   * Метод пытается создать вызывающий каталог в файловой системе.
   * @param int $mode Маска доступа.
   * @throws exceptions\DuplicationException Выбрасывается в случае, если создание компонента приведет к дублированию.
   * @throws exceptions\InvalidArgumentException Выбрасывает в случае, если в качестве маски доступа передано значение не integer типа.
   * @return bool true - в случае успеха, иначе - false.
   */
  public function create($mode = 0777){
    exceptions\InvalidArgumentException::verifyType($mode, 'i');
    if($this->isExists()){
      throw new exceptions\DuplicationException('Невозможно выполнить действие. Компонент с данным именем уже существует.');
    }

    $nameDir = $this->getAddress();
    return mkdir($nameDir, $mode);
  }

  /**
   * Метод пытается создать новый каталог в вызывающем каталоге и возвращает его представление в случае успеха.
   *
   * @param string $dirName Имя создаваемого каталога.
   * @param int $mode Маска доступа
   * .
   * @throws NotExistsException Выбрасывается в случае, если вызываемого каталога не существует.
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws exceptions\DuplicationException Выбрасывает в случае, если указанный каталог уже существует.
   * @return Directory Созданный каталог.
   */
  public function createDir($dirName, $mode = 0777){
    exceptions\InvalidArgumentException::verifyType($mode, 'i');
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    if($this->isDirExists($dirName)){
      throw new exceptions\DuplicationException('Указанный компонент уже существует в файловой системе.');
    }
    $dir = new Directory($dirName, $this);
    $dir->create($mode); // Перехват исключений не выполняется в связи с невозможностью их появления
    return $dir;
  }

  /**
   * Метод пытается создать новый файл в вызывающем каталоге и возвращает его представление в случае успеха.
   *
   * @param string $dirName Имя создаваемого файла.
   * @param int $mode Маска доступа
   * .
   * @throws NotExistsException Выбрасывается в случае, если вызываемого каталога не существует.
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws exceptions\DuplicationException Выбрасывает в случае, если указанный файл уже существует.
   * @return File Созданный файл.
   */
  public function createFile($fileName, $mode = 0777){
    exceptions\InvalidArgumentException::verifyType($mode, 'i');
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    if($this->isFileExists($fileName)){ // Перехват исключений не выполняется в связи с невозможностью их появления
      throw new exceptions\DuplicationException('Указанный компонент уже существует в файловой системе.');
    }

    $file = new File($fileName, $this);
    $file->create($mode); // Перехват исключений не выполняется в связи с невозможностью их появления
    return $file;
  }

  /**
   * Возвращает итератор для данного каталога.
   * @return \DirectoryIterator Итератор вызывающего каталога.
   */
  public function getDirectoryIterator(){
    return new \DirectoryIterator($this->getAddress());
  }

  /**
   * Возвращает компонент каталога, имя которого заданно в аргументе.
   * @param string $fileName Имя компонента.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @throws NotExistsException Выбрасывается в случае, если вызываемого или получаемого каталога на момент вызова метода не существует.
   * @return File Получаемый компонент.
   */
  public function getFile($fileName){
    exceptions\InvalidArgumentException::verifyType($fileName, 'S');
    // Перехват исключений не выполняется в связи с невозможностью их появления
    if(!$this->isExists() || !$this->isFileExists($fileName)){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    return new File($fileName, $this);
  }

  /**
   * Возвращает компонент каталога, имя которого заданно в аргементе.
   * @param string $dirName Имя компонента.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @throws NotExistsException Выбрасывается в случае, если вызываемого или получаемого каталога на момент вызова метода не существует.
   * @return Directory Получаемый компонент.
   */
  public function getDir($dirName){
    exceptions\InvalidArgumentException::verifyType($dirName, 'S');
    // Перехват исключений не выполняется в связи с невозможностью их появления
    if(!$this->isExists() || !$this->isDirExists($dirName)){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    return new Directory($dirName, $this);
  }

  /**
   * Метод возвращает массив имен компонентов вызывающего каталога.
   * @param string $mask Маска поиска.
   * @throws exceptions\InvalidArgumentException Выбрасывает в случае, если в качестве маски передано значение не string типа.
   * @throws NotExistsException Выбрасывается в случае, если вызываемого компонента не существует.
   * @return string[] Массив имен компонентов вызывающего каталога.
   */
  public function getNamesComponents($mask = '*'){
    exceptions\InvalidArgumentException::verifyType($mask, 'S');
    // Перехват исключений не выполняется в связи с невозможностью их появления
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }

    $result = glob($this->getAddress() . '/' . $mask);
    foreach($result as $key => $address){
      $result[$key] = substr($address, strrpos($address, '/') + 1);
    }
    return $result;
  }

  /**
   * Метод проверяет, имеется ли в вызывающем каталоге заданный файл.
   *
   * @param $fileName Имя проверяемого файла.
   *
   * @throws NotExistsException Выбрасывается в случае, если вызываемого компонента не существует.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @return bool true - если в вызывающем каталоге имеется заданный файл, иначе - false.
   */
  public function isFileExists($fileName){
    exceptions\InvalidArgumentException::verifyType($fileName, 'S');
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе родительский каталог ['.$this->getAddress().'] не найден.');
    }
    $fileAddress = $this->getAddress() . '/' . $fileName;
    return (file_exists($fileAddress) && is_file($fileAddress));
  }

  /**
   * Метод проверяет, имеется ли в вызывающем каталоге заданный каталог.
   *
   * @param $dirName Имя проверяемого каталога.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра недопустимого типа.
   * @throws NotExistsException Выбрасывается в случае, если вызываемого компонента не существует.
   * @return bool true - если в вызывающем каталоге имеется заданный каталог, иначе - false.
   */
  public function isDirExists($dirName){
    exceptions\InvalidArgumentException::verifyType($dirName, 'S');
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден ('.$this->getAddress().').');
    }
    $dirAddress = $this->getAddress() . '/' . $dirName;
    return (file_exists($dirAddress) && is_dir($dirAddress));
  }
}
