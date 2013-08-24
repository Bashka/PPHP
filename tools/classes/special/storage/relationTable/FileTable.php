<?php
namespace PPHP\tools\classes\special\storage\relationTable;

/**
 * Класс реализует реляционное хранилище с использование файлов в качестве хранилищ.
 */
class FileTable implements Storage{
  /**
   * Файл таблицы.
   * @var \PPHP\tools\classes\standard\fileSystem\File
   */
  private $fileTable;

  /**
   * Общее количество записей в таблице.
   * @var integer
   */
  private $countRecord;

  /**
   * @var \PPHP\tools\classes\special\storage\relationTable\Table
   */
  private $table;

  /**
   * Метод создает согласно заданной структуре и возвращает ссылку на файлово-табличное хранилище. Метод создает файл, если его не существует на момент вызова метода.
   * @static
   * @param Structure $structure Структура создаваемого хранилища.
   * @param \PPHP\tools\classes\standard\fileSystem\File $fileTable Файл, который отвечает за хранение таблицы.
   * @return \PPHP\tools\classes\special\storage\relationTable\FileTable Ссылка на экземпляр данного класса, связанного с создаваемой таблицей.
   */
  static public function createTable(Structure $structure, \PPHP\tools\classes\standard\fileSystem\File $fileTable){
    if(!$fileTable->isExists()){
      $fileTable->create();
    }
    $writer = $fileTable->getWriter();
    $writer->clean();
    $writer->write(serialize($structure) . "\n");
    $writer->close();

    return new FileTable($fileTable);
  }

  function __construct(\PPHP\tools\classes\standard\fileSystem\File $fileTable){
    $this->fileTable = $fileTable;
    $reader = $this->fileTable->getReader();
    $structure = unserialize($reader->readLine());
    $reader->close();
    $sizeStructure = $structure->getSize();
    $dataSize = $structure->getSizeData();
    $pointer = new Pointer($sizeStructure + 1, $dataSize);
    $this->table = new Table($structure, $pointer);
    $fileSize = $this->fileTable->getSize();
    $this->countRecord = ($fileSize - $sizeStructure - 1) / $dataSize;
  }

  /**
   * Метод считывает заданную строку данных и возвращает ее объектное представление. Если данной записи не существует или она была предворительно удалена, метод вернет null.
   * @param integer $id Номер записи.
   * @throws FileException Выбрасывается в случае, если произошла ошибка при обращении к файлу.
   * @throws \OutOfRangeException Выбрасывается в случае, если производится попытка получить запись, которой нет в таблице.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве аргумента были переданы данные недопустимого типа.
   * @return array|null Массив данных или null, если заданная запись была стерта.
   */
  public function select($id){
    if($id < 0 || $this->countRecord < $id){
      throw new \OutOfRangeException();
    }
    try{
      $reader = $this->fileTable->getReader();
    }
    catch(\RuntimeException $ext){
      throw new FileException(null, null, $ext);
    }
    $result = $this->table->select($id, $reader);
    $reader->close();

    return $result;
  }

  /**
   * Метод изменяет значение записи на заданное.
   * @param integer $id Индекс записи.
   * @param array $data Данные, на которые должена быть заменена запись.
   * @throws FileException Выбрасывается в случае, если произошла ошибка при обращении к файлу.
   * @throws \OutOfRangeException Выбрасывается в случае, если производится попытка получить запись, которой нет в таблице.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве аргумента были переданы данные недопустимого типа.
   * @return boolean true - если выполнение метода привело к изменениям, иначе - false.
   */
  public function update($id, array $data){
    if($id < 0 || $this->countRecord < $id){
      throw new \OutOfRangeException();
    }
    try{
      $writer = $this->fileTable->getWriter();
    }
    catch(\RuntimeException $ext){
      throw new FileException(null, null, $ext);
    }
    $result = $this->table->update($id, $writer, $data);
    $writer->close();

    return $result;
  }

  /**
   * Метод стирает указанную запись.
   * @param integer $id Индекс записи.
   * @throws FileException Выбрасывается в случае, если произошла ошибка при обращении к файлу.
   * @throws \OutOfRangeException Выбрасывается в случае, если производится попытка получить запись, которой нет в таблице.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве аргумента были переданы данные недопустимого типа.
   * @return boolean true - если выполнение метода привело к изменениям, иначе - false.
   */
  public function delete($id){
    if($id < 0 || $this->countRecord < $id){
      throw new \OutOfRangeException();
    }
    try{
      $writer = $this->fileTable->getWriter();
    }
    catch(\RuntimeException $ext){
      throw new FileException(null, null, $ext);
    }
    $result = $this->table->delete($id, $writer);
    $writer->close();

    return $result;
  }

  /**
   * Метод вставляет новую запись в конец таблицы.
   * @param array $data Новая, вставляемая запись.
   * @throws FileException Выбрасывается в случае, если произошла ошибка при обращении к файлу.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве аргумента были переданы данные недопустимого типа.
   * @return boolean true - если выполнение метода привело к изменениям, иначе - false.
   */
  public function insert(array $data){
    $this->countRecord++;
    try{
      $writer = $this->fileTable->getWriter();
    }
    catch(\RuntimeException $ext){
      throw new FileException(null, null, $ext);
    }
    $result = $this->table->update($this->countRecord, $writer, $data);
    $writer->close();

    return $result;
  }

  /**
   * Метод возвращает текущее количество записей в таблице с учетом стертых записей.
   * @return integer Текущее количество записей.
   */
  public function getCountRecord(){
    return $this->countRecord;
  }
}
