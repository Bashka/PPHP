<?php
namespace PPHP\tools\classes\standard\fileSystem;

/**
 * Класс представляет файл файловой системы и предоставляет входные выходные потоки для работы с содержимым.
 */
class File extends ComponentFileSystem implements \SplObserver{
  /**
   * Текущий входной поток данного файла.
   * @var \PPHP\tools\classes\standard\fileSystem\io\BlockingFileReader
   */
  protected $reader;
  /**
   * Текущий выходной поток данного файла.
   * @var \PPHP\tools\classes\standard\fileSystem\io\BlockingFileWriter
   */
  protected $writer;

  /**
   * Метод снимает блокировку файла при закрытии потока. Метод реагирует только в том случае, если он был вызван текущим блокирующим потоком.
   * (PHP 5 &gt;= 5.1.0)<br/>
   * Receive update from subject
   * @link http://php.net/manual/en/splobserver.update.php
   * @param \SplSubject $subject <p>
   * The <b>SplSubject</b> notifying the longObject of an update.
   * </p>
   * @return void
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
   * Метод изменяет имя компонента на заданное, если это возможно.
   * @param string $newName Новое имя компонента.
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
    if($this->getLocation()->isFileExists($newName)){
      throw new ComponentDuplicationException('Невозможно выполнить действие. Компонент с данным именем уже существует.');
    }
    return parent::rename($newName);
  }

  /**
   * Метод перемещает компонент в данный каталог.
   * @param Directory $location Целевой каталог.
   * @throws ComponentDuplicationException Выбрасывается в случае, если целевой каталог уже содержит компонент с тем же именем, что и перемещаемый.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return boolean
   */
  public function move(Directory $location){
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    if($location->isFileExists($this->getName())){
      throw new ComponentDuplicationException('Невозможно выполнить действие. Компонент с данным именем уже существует.');
    }
    return parent::move($location);
  }

  /**
   * Метод копирует компонента в данный каталог.
   * @param Directory $location Целевой каталог.
   * @throws ComponentDuplicationException Выбрасывается в случае, если целевой каталог уже содержит копируемый компонент.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return boolean
   */
  public function copyPaste(Directory $location){
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    if($location->isFileExists($this->getName())){
      throw new ComponentDuplicationException('Невозможно выполнить действие. Компонент с данным именем уже существует.');
    }
    return copy($this->getAddress(), $location->getAddress() . '/' . $this->getName());
  }

  /**
   * Метод возвращает размер в байтах данного компонента.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return integer
   */
  public function getSize(){
    if(!$this->isExists()){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе компонент не найден.');
    }
    return filesize($this->getAddress());
  }

  /**
   * Метод определяет, существует ли вызывающий компонент на момент вызова метода.
   * @throws NotExistsException Выбрасывается в случае, если родительского каталога не существует.
   * @return boolean true - если компонент на момент вызова метода существует в файловой системе, иначе - false.
   */
  public function isExists(){
    try{
      return $this->getLocation()->isFileExists($this->getName());
    }
    catch(NotExistsException $exception){
      throw new NotExistsException('Невозможно выполнить действие. В файловой системе родительский каталог не найден ('.$this->getAddress().').', 0, $exception);
    }
  }

  /**
   * Метод пытается создать вызывающий файл в файловой системе.
   * @throws ComponentDuplicationException Выбрасывается в случае, если создание компонента приведет к дублированию.
   * @throws NotExistsException Выбрасывается в случае, если родительского каталога не существует.
   * @return bool true - в случае успеха, иначе - false.
   */
  public function create(){
    if($this->isExists()){
      throw new ComponentDuplicationException('Невозможно выполнить действие. Компонент с данным именем уже существует.');
    }
    fclose(fopen($this->getAddress(), 'a+'));
    return true;
  }

  /**
   * Метод возвращает входной поток для данного файла и блокирует его разделяемой блокировкой. В сдучае, если полученный поток будет закрыт, блокировка снимется автоматически.
   * @throws LockException Выбрасывается в случае, если невозможно вернуть поток из за того, что уже был открыт выходной поток.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return \PPHP\tools\classes\standard\fileSystem\io\BlockingFileReader
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
    $this->reader = new \PPHP\tools\classes\standard\fileSystem\io\BlockingFileReader($des);
    $this->reader->attach($this);
    return $this->reader;
  }

  /**
   * Метод возвращает выходной поток для данного файла и блокирует его исключительной блокировкой. В сдучае, если полученный поток будет закрыт, блокировка снимется автоматически.
   * @throws LockException Выбрасывается в случае, если невозможно вернуть поток из за того, что уже был открыт выходной поток.
   * @throws NotExistsException Выбрасывается в случае, если на момент вызова метода компонента или родительского каталога компонента не существовало.
   * @return \PPHP\tools\classes\standard\fileSystem\io\BlockingFileWriter
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
    $this->writer = new \PPHP\tools\classes\standard\fileSystem\io\BlockingFileWriter($des);
    $this->writer->attach($this);
    return $this->writer;
  }

  /**
   * Метод удаляет текущий компонент из файловой системы.
   * @throws LockException Выбрасывается в случае, если на момент удаления компонента он блокирован открытым потоком.
   * @throws NotExistsException Выбрасывается в случае, если удаляемого компонента не существует.
   * @return boolean true - если компонент был успешно удален.
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
   * Метод возвращает расришение файла.
   * @return string Расширение файла.
   */
  public function getType(){
    $name = $this->getName();
    return substr($name, stripos($name, '.')+1);
  }
}
