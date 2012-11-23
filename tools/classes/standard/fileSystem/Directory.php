<?php
namespace PPHP\tools\classes\standard\fileSystem;

/**
 * Класс представляет каталог файловой системы в программе и предоставляет механизмы доступа к его содержимому.
 */
class Directory extends ComponentFileSystem{
  /**
   * Метод изменяет имя компонента на заданное, если это возможно.
   * @param string $newName Новое имя компонента
   * @throws ComponentDuplicationException Выбрасывается в случае, если переименование компонента приведет к дублированию.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывает в случае, если в качестве нового имени передано значение не string типа, или если новое имя содержит симол /.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return boolean
   */
  public function rename($newName){
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    if(!is_string($newName) || strpos($newName, '/') > -1){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $newName);
    }

    if($this->getLocation()->isDirExists($newName)){
      throw new ComponentDuplicationException('Невозможно выполнить действие. Компонент с данным именем уже существует.');
    }
    return parent::rename($newName);
  }

  /**
   * Метод перемещает компонент в данный каталог.
   * @param Directory $location Целевой каталог.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException Выбрасывается при попытке переместить вызывающий каталог в самого себя.
   * @throws ComponentDuplicationException Выбрасывается в случае, если целевой каталог уже содержит компонент с тем же именем, что и перемещаемый.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return boolean
   */
  public function move(Directory $location){
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден ('.$this->getAddress().').');
    }
    if($location->isDirExists($this->getName())){
      throw new ComponentDuplicationException('Невозможно выполнить действие. Компонент с данным именем уже существует.');
    }
    if($location->getAddress() == $this->getAddress()){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException('Ожидается отличный от данного каталога каталог.');
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
   * @param Directory $location Целевой каталог.
   * @throws ComponentDuplicationException Выбрасывается в случае, если целевой каталог уже содержит копируемый компонент.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return boolean true - если выполнение метода привело в изменению файловой системы, иначе false.
   */
  public function copyPaste(Directory $location){
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    if($location->isDirExists($this->getName())){
      throw new ComponentDuplicationException('Невозможно выполнить действие. Компонент с данным именем уже существует.');
    }
    $copyDirRoot = new Directory($this->getName(), $location);
    if(!$copyDirRoot->create()){
      return false;
    }
    $iterator = $this->getDirectoryIterator();
    foreach($iterator as $component){
      if($component == '.' || $component == '..'){
        continue;
      }
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
   * @throws LockException Выбрасывается в случае, если на момент удаления компонента какой либо из вложенных файлов блокирован открытым потоком.
   * @throws NotExistsException Выбрасывается в случае, если удаляемого компонента не существует.
   * @return boolean true - если компонент был успешно удален.
   */
  public function delete(){
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    $this->clear();
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
   * @throws ComponentDuplicationException Выбрасывается в случае, если создание компонента приведет к дублированию.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывает в случае, если в качестве маски доступа передано значение не integer типа.
   * @throws NotExistsException Выбрасывается в случае, если родительского каталога не существует.
   * @return bool true - в случае успеха, иначе - false.
   */
  public function create($mode = 0777){
    if(!is_integer($mode)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('integer', $mode);
    }
    $nameDir = $this->getAddress();
    if($this->isExists()){
      throw new ComponentDuplicationException('Невозможно выполнить действие. Компонент с данным именем уже существует.');
    }
    return mkdir($nameDir, $mode);
  }

  /**
   * Метод пытается создать новый каталог в вызывающем каталоге и возвращает его представление в случае успеха.
   * @param string $dirName Имя создаваемого каталога.
   * @param int $mode Маска доступа.
   * @throws NotExistsException Выбрасывается в случае, если вызываемого каталога не существует.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException Выбрасывает в случае, если указанный каталог уже существует.
   * @return \PPHP\tools\classes\standard\fileSystem\Directory Созданный каталог.
   */
  public function createDir($dirName, $mode = 0777){
    if(!is_integer($mode)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('integer', $mode);
    }
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    if($this->isDirExists($dirName)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException('Указанный компонент уже существует в файловой системе.');
    }
    $dir = new Directory($dirName, $this);
    $dir->create($mode);
    return $dir;
  }

  public function createFile($fileName, $mode = 0777){
    if(!is_integer($mode)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('integer', $mode);
    }
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    if($this->isFileExists($fileName)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException('Указанный компонент уже существует в файловой системе.');
    }
    $file = new File($fileName, $this);
    $file->create($mode);
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
   * @param $fileName Имя компонента.
   * @return \PPHP\tools\classes\standard\fileSystem\File Получаемый компонент.
   * @throws NotExistsException Выбрасывается в случае, если вызываемого или получаемого каталога на момент вызова метода не существует.
   */
  public function getFile($fileName){
    if(!$this->isExists() || !$this->isFileExists($fileName)){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    return new File($fileName, $this);
  }

  /**
   * Возвращает компонент каталога, имя которого заданно в аргементе.
   * @param $dirName Имя компонента.
   * @return \PPHP\tools\classes\standard\fileSystem\Directory Получаемый компонент.
   * @throws NotExistsException Выбрасывается в случае, если вызываемого или получаемого каталога на момент вызова метода не существует.
   */
  public function getDir($dirName){
    if(!$this->isExists() || !$this->isDirExists($dirName)){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    return new Directory($dirName, $this);
  }

  /**
   * Метод возвращает массив имен компонентов вызывающего каталога.
   * @param string $mask Маска поиска.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывает в случае, если в качестве маски передано значение не string типа.
   * @throws NotExistsException Выбрасывается в случае, если вызываемого компонента не существует.
   * @return array Массив имен компонентов вызывающего каталога.
   */
  public function getNamesComponents($mask = '*'){
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    if(!is_string($mask)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $mask);
    }
    $result = glob($this->getAddress() . '/' . $mask);
    foreach($result as $key => $address){
      $result[$key] = substr($address, strrpos($address, '/') + 1);
    }
    return $result;
  }

  /**
   * Метод проверяет, имеется ли в вызывающем каталоге заданный файл.
   * @param $fileName Имя проверяемого файла.
   * @throws NotExistsException Выбрасывается в случае, если вызываемого компонента не существует.
   * @return bool true - если в вызывающем каталоге имеется заданный файл, иначе - false.
   */
  public function isFileExists($fileName){
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    $fileAddress = $this->getAddress() . '/' . $fileName;
    return (file_exists($fileAddress) && is_file($fileAddress));
  }

  /**
   * Метод проверяет, имеется ли в вызывающем каталоге заданный каталог.
   * @param $dirName Имя проверяемого каталога.
   * @throws NotExistsException Выбрасывается в случае, если вызываемого компонента не существует.
   * @return bool true - если в вызывающем каталоге имеется заданный каталог, иначе - false.
   */
  public function isDirExists($dirName){
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден ('.$this->getAddress().').');
    }
    $dirAddress = $this->getAddress() . '/' . $dirName;
    return (file_exists($dirAddress) && is_dir($dirAddress));
  }
}
