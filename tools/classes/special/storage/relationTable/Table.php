<?php
namespace PPHP\tools\classes\special\storage\relationTable;

/**
 * Класс позволяет объединить структуру и указатель в единый механизм обработки данных в соответствии с заданной структурой.
 */
class Table{
  /**
   * Структура данных таблицы.
   * @var \PPHP\tools\classes\special\storage\relationTable\Structure
   */
  private $structure;

  /**
   * Указатель таблицы.
   * @var \PPHP\tools\classes\special\storage\relationTable\Pointer
   */
  private $pointer;

  /**
   * Длина записи.
   * @var integer
   */
  private $sizeData;

  /**
   * @param Structure $structure Структура данных таблицы.
   * @param Pointer $pointer Указатель таблицы.
   */
  function __construct(Structure $structure, Pointer $pointer){
    $this->structure = $structure;
    $this->sizeData = $this->structure->getSizeData();
    $this->pointer = $pointer;
  }

  /**
   * Метод считывает заданную строку данных и возвращает ее объектное представление. Если данной записи не существует или она была предворительно удалена, метод вернет null.
   * @param integer $id Номер записи.
   * @param \PPHP\tools\patterns\io\Reader|\PPHP\tools\patterns\io\SeekIO $reader Источник данных. Должен реализовать оба указанных интерфейса.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве аргумента были переданы данные недопустимого типа.
   * @return array|null Массив данных или null, если заданная запись была стерта.
   */
  public function select($id, $reader){
    if(!($reader instanceof \PPHP\tools\patterns\io\Reader) || !($reader instanceof \PPHP\tools\patterns\io\SeekIO)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException();
    }
    if(!is_int($id)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('integer', $id);
    }
    $this->pointer->setLine($id);
    $reader->setPosition($this->pointer->getPosition());
    $data = '';
    $i = $this->sizeData;
    while($i--){
      $data .= $reader->read();
    }
    if(trim($data) == ''){
      return null;
    }
    $this->pointer->nextPosition();
    $result = $this->structure->unserializeData($data);

    return $result;
  }

  /**
   * Метод изменяет значение записи на заданное.
   * @param integer $id Индекс записи.
   * @param \PPHP\tools\patterns\io\Writer|\PPHP\tools\patterns\io\SeekIO $writer Выходной поток. Должен реализовать оба указанных интерфейса.
   * @param array $data Данные, на которые должена быть заменена запись.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве аргумента были переданы данные недопустимого типа.
   * @return boolean true - если выполнение метода привело к изменениям, иначе - false.
   */
  public function update($id, $writer, array $data){
    if(!($writer instanceof \PPHP\tools\patterns\io\Writer) || !($writer instanceof \PPHP\tools\patterns\io\SeekIO)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException();
    }
    if(!is_int($id)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('integer', $id);
    }
    $this->pointer->setLine($id);
    $writer->setPosition($this->pointer->getPosition());
    $data = $this->structure->serializeData($data);
    $i = 0;
    while($i < $this->sizeData){
      $writer->write(substr($data, $i++, 1));
    }
    $this->pointer->nextPosition();

    return true;
  }

  /**
   * Метод стирает указанную запись.
   * @param integer $id Индекс записи.
   * @param \PPHP\tools\patterns\io\Writer|\PPHP\tools\patterns\io\SeekIO $writer Выходной поток. Должен реализовать оба указанных интерфейса.
   * @return boolean true - если выполнение метода привело к изменениям, иначе - false.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае, если в качестве аргумента были переданы данные недопустимого типа.
   */
  public function delete($id, $writer){
    if(!($writer instanceof \PPHP\tools\patterns\io\Writer) || !($writer instanceof \PPHP\tools\patterns\io\SeekIO)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException();
    }
    if(!is_int($id)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('integer', $id);
    }
    $this->pointer->setLine($id);
    $writer->setPosition($this->pointer->getPosition());
    $i = $this->sizeData;
    while($i--){
      $writer->write(' ');
    }
    $this->pointer->nextPosition();

    return true;
  }
}