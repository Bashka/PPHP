<?php
namespace PPHP\tools\classes\standard\fileSystem;

use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет файл файловой системы и предоставляет входные/выходные потоки для работы с содержимым.
 * @author  Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\fileSystem
 */
class File extends ComponentFileSystem implements \SplObserver{
  /**
   * @var \PPHP\tools\classes\standard\fileSystem\io\BlockingFileReader Текущий входной поток данного файла.
   */
  protected $reader;

  /**
   * @var \PPHP\tools\classes\standard\fileSystem\io\BlockingFileWriter Текущий выходной поток данного файла.
   */
  protected $writer;

  /**
   * Метод снимает блокировку файла при закрытии потока. Метод реагирует только в том случае, если он был вызван текущим блокирующим потоком.
   * Receive update from subject
   * @link http://php.net/manual/en/splobserver.update.php
   * @param \SplSubject $subject The SplSubject notifying the longObject of an update.
   */
  public function update(\SplSubject $subject){
    if(isset($this->reader) && $subject === $this->reader && $this->reader->isClose()){
      unset($this->reader);
    }
    elseif(isset($this->writer) && $subject === $this->writer && $this->writer->isClose()){
      unset($this->writer);
    }
  }

  /**
   * @prototype \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem
   */
  public function rename($newName){
    if(strpos($newName, '/') > -1){
      throw exceptions\InvalidArgumentException::getValidException('[^/]', $newName);
    }
    if($this->getLocation()->isFileExists($newName)){
      throw new exceptions\DuplicationException('Невозможно выполнить действие. Компонент с данным именем уже существует.');
    }

    return parent::rename($newName);
  }

  /**
   * @prototype \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem
   */
  public function move(Directory $location){
    if($location->isFileExists($this->getName())){
      throw new exceptions\DuplicationException('Невозможно выполнить действие. Компонент с данным именем уже существует.');
    }

    return parent::move($location);
  }

  /**
   * @prototype \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem
   */
  public function copyPaste(Directory $location){
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    if($location->isFileExists($this->getName())){
      throw new exceptions\DuplicationException('Невозможно выполнить действие. Компонент с данным именем уже существует.');
    }
    if(!copy($this->getAddress(), $location->getAddress() . '/' . $this->getName())){
      return false;
    }
    else{
      return new File($this->getName(), $location);
    }
  }

  /**
   * @prototype \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem
   */
  public function getSize(){
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }

    return filesize($this->getAddress());
  }

  /**
   * @prototype \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem
   */
  public function isExists(){
    try{
      return $this->getLocation()->isFileExists($this->getName());
    }
    catch(NotExistsException $exception){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе родительский каталог [' . $this->getLocation()->getName() . '] не найден [' . $this->getAddress() . '].', 0, $exception);
    }
    // Дальнейший перехват исключений не выполняется в связи с невозможностью их появления
  }

  /**
   * @prototype \PPHP\tools\classes\standard\fileSystem\ComponentFileSystem
   */
  public function delete(){
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. Компонента не существует.');
    }
    if(!empty($this->reader) || !empty($this->writer)){
      throw new LockException('Доступ к данному компоненту запрещен.');
    }

    return unlink($this->getAddress());
  }

  /**
   * Метод пытается создать вызываемый файл в файловой системе.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException Выбрасывается в случае, если создание компонента приведет к дублированию.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывает в случае, если в качестве маски доступа передано значение не integer типа.
   * @return boolean true - в случае успеха, иначе - false.
   */
  public function create(){
    if($this->isExists()){
      throw new exceptions\DuplicationException('Невозможно выполнить действие. Компонент с данным именем уже существует.');
    }
    fclose(fopen($this->getAddress(), 'a+'));

    return true;
  }

  /**
   * Метод возвращает входной поток для данного файла и блокирует его разделяемой блокировкой. В сдучае, если полученный поток будет закрыт, блокировка снимется автоматически.
   * @throws \PPHP\tools\classes\standard\fileSystem\LockException Выбрасывается в случае, если невозможно вернуть поток из за того, что уже был открыт выходной поток.
   * @throws \PPHP\tools\classes\standard\fileSystem\NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return \PPHP\tools\classes\standard\fileSystem\io\BlockingFileReader Файловый поток ввода.
   */
  public function getReader(){
    if(!empty($this->reader)){
      return $this->reader;
    }
    if(!empty($this->writer)){
      throw new LockException('Доступ к данному компоненту запрещен.');
    }
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    $des = fopen($this->getAddress(), 'r+b');
    flock($des, 1);
    $this->reader = new io\BlockingFileReader($des);
    $this->reader->attach($this);

    return $this->reader;
  }

  /**
   * Метод возвращает выходной поток для данного файла и блокирует его исключительной блокировкой. В сдучае, если полученный поток будет закрыт, блокировка снимется автоматически.
   * @throws \PPHP\tools\classes\standard\fileSystem\LockException Выбрасывается в случае, если невозможно вернуть поток из за того, что уже был открыт выходной поток.
   * @throws \PPHP\tools\classes\standard\fileSystem\NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return \PPHP\tools\classes\standard\fileSystem\io\BlockingFileWriter Файловый поток вывода.
   */
  public function getWriter(){
    if(!empty($this->writer)){
      return $this->writer;
    }
    if(!empty($this->reader)){
      throw new LockException('Доступ к данному компоненту запрещен.');
    }
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    $des = fopen($this->getAddress(), 'r+b');
    flock($des, 2);
    $this->writer = new io\BlockingFileWriter($des);
    $this->writer->attach($this);

    return $this->writer;
  }

  /**
   * Метод возвращает расришение файла.
   * @return string Расширение файла.
   */
  public function getType(){
    /**
     * @var string Полное имя файла.
     */
    $name = $this->getName();
    $p = stripos($name, '.');
    if($p !== false){
      return substr($name, $p + 1);
    }
    else{
      return '';
    }
  }
}
