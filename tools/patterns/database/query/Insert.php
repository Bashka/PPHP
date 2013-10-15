<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет объектную SQL инструкцию для вставки записи в таблицу.
 * Объекты данного класса могут быть восстановлены из строки следующего формата: INSERT INTO `имяТаблицы` (`имяПоля`|имяТаблицы.имяПоля, ...) VALUES ("данные", ...).
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Insert extends ComponentQuery{
  /**
   * @var \PPHP\tools\patterns\database\query\Table Целевая таблица.
   */
  private $table;

  /**
   * @var \PPHP\tools\patterns\database\query\Field[] Множество используемых в запросе полей таблицы.
   */
  private $fields;

  /**
   * @var mixed[] Множество устанавливаемых значений записи.
   */
  private $values;

  /**
   * @var \PPHP\tools\patterns\database\query\Select SQL инструкция, возвращающая добавляемое множество значений записи(ей).
   */
  private $select;

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getMasks($driver = null){
    return ['INSERT INTO `(' . Table::getPatterns()['tableName'] . ')` (\((' . Field::getMasks()[0] . '|' . Field::getMasks()[1] . ')(, ?(' . Field::getMasks()[0] . '|' . Field::getMasks()[1] . '))*\) VALUES \(' . LogicOperation::getPatterns()['stringValue'] . '(, ?' . LogicOperation::getPatterns()['stringValue'] . ')*\))'];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Restorable
   */
  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    $mask = parent::reestablish($string);
    $o = new self(Table::reestablish($mask[1]));
    $data = explode('VALUES', $mask[2]);
    // Обработка полей
    $fields = explode(',', substr(trim($data[0]), 1, -1));
    // Обработка значений
    $values = explode(',', substr(trim($data[1]), 1, -1));
    // Запись данных в запрос
    foreach($fields as $k => $v){
      $o->addData(Field::reestablish(trim($v)), substr(trim($values[$k]), 1, -1));
    }

    return $o;
  }

  /**
   * @param \PPHP\tools\patterns\database\query\Table $table Целевая таблица.
   */
  function __construct(Table $table){
    $this->fields = [];
    $this->values = [];
    $this->table = $table;
  }

  /**
   * Метод добавляет данные в запрос.
   * @param \PPHP\tools\patterns\database\query\Field $field Целевое поле.
   * @param string|number|boolean $value Значение поля.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException Выбрасывается в случае, если указанное поле уже присутствует в запросе.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return \PPHP\tools\patterns\database\query\Insert Метод возвращает вызываемый объект.
   */
  public function addData(Field $field, $value){
    if(array_search($field, $this->fields) !== false){
      throw new exceptions\DuplicationException('Ошибка дублирования компонента.');
    }
    exceptions\InvalidArgumentException::verifyType($value, 'sifb');
    $this->fields[] = $field;
    $this->values[] = $value;

    return $this;
  }

  /**
   * Метод устанавливает запрос, возвращающий данные для добавления.
   * @param \PPHP\tools\patterns\database\query\Select $select SELECT запрос, возвращающий данные для добавления.
   * @return \PPHP\tools\patterns\database\query\Insert Метод возвращает вызываемый объект.
   */
  public function setSelect(Select $select){
    $this->select = $select;

    return $this;
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Interpreter
   */
  public function interpretation($driver = null){
    exceptions\InvalidArgumentException::verifyType($driver, 'Sn');
    if(count($this->values) == 0 && !is_object($this->select)){
      throw new exceptions\NotFoundDataException('Нет данных для формирования строки.');
    }
    try{
      $resultString = 'INSERT INTO `' . $this->table->interpretation($driver) . '` ';
      // Генерация запроса с данными вложенного запроса
      if(is_object($this->select)){
        $resultString .= $this->select->interpretation($driver);
      }
      else{
        $resultString .= '(';
        foreach($this->fields as $field){
          $resultString .= $field->interpretation($driver) . ',';
        }
        $resultString = substr($resultString, 0, -1);
        // Генерация запроса с константными данными
        $resultString .= ') VALUES (';
        foreach($this->values as $val){
          $resultString .= '"' . $val . '",';
        }
        $resultString = substr($resultString, 0, strlen($resultString) - 1);
        $resultString .= ')';
      }

      return $resultString;
    }
    catch(exceptions\NotFoundDataException $exc){
      throw $exc;
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * @return \PPHP\tools\patterns\database\query\Table
   */
  public function getTable(){
    return $this->table;
  }

  public function getFields(){
    return $this->fields;
  }

  public function getValues(){
    return $this->values;
  }

  /**
   * @return \PPHP\tools\patterns\database\query\Select
   */
  public function getSelect(){
    return $this->select;
  }
}
