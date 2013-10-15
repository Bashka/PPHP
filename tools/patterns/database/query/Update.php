<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет объектную SQL инструкцию для обновления данных в БД.
 * Объекты данного класса могут быть восстановлены из строки следующего формата: UPDATE `имяТаблицы` SET `имяПоля`|имяТаблицы.имяПоля = "значение", ... [WHERE (логическоеВыражение)].
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Update extends ComponentQuery{
  /**
   * @var \PPHP\tools\patterns\database\query\Table Целевая таблица.
   */
  private $table;

  /**
   * @var \PPHP\tools\patterns\database\query\Field[] Множество полей, используемых в таблице.
   */
  private $fields;

  /**
   * @var mixed[] Множество значений, устанавливаемых в поля записи.
   */
  private $values;

  /**
   * @var \PPHP\tools\patterns\database\query\Where Условие отбора.
   */
  private $where;

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getMasks($driver = null){
    return ['UPDATE `(' . Table::getPatterns()['tableName'] . ')` SET (' . self::getPatterns()['setValue'] . '(, ?' . self::getPatterns()['setValue'] . ')*)( ' . Where::getMasks()[0] . ')?'];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getPatterns($driver = null){
    return ['setValue' => '(' . Field::getMasks()[0] . '|' . Field::getMasks()[1] . ') = ' . LogicOperation::getPatterns()['stringValue']];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Restorable
   */
  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    $mask = parent::reestablish($string);
    $o = new self(Table::reestablish($mask[1]));
    $data = explode(',', $mask[2]);
    // Запись данных в запрос
    foreach($data as $v){
      $v = explode('=', $v);
      $o->addData(Field::reestablish(trim($v[0])), substr(trim($v[1]), 1, -1));
    }
    if(($p = strrpos($string, 'WHERE')) !== false){
      $o->insertWhere(Where::reestablish(substr($string, $p)));
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
   * Метод устанавливает условие отбора для запроса.
   * @param \PPHP\tools\patterns\database\query\Where $where Условие отбора.
   * @return Update Метод возвращает вызываемый объект.
   */
  public function insertWhere(Where $where){
    $this->where = $where;

    return $this;
  }

  /**
   * Метод добавляет данные в запрос.
   * @param \PPHP\tools\patterns\database\query\Field $field Целевое поле.
   * @param string|number|boolean $value Значение целевого поля.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException Выбрасывается в случае, если указанное поле уже присутствует в запросе.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return Update Метод возвращает вызываемый объект.
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
   * @prototype \PPHP\tools\patterns\interpreter\Interpreter
   */
  public function interpretation($driver = null){
    if(count($this->values) == 0){
      throw new exceptions\NotFoundDataException('Недостаточно данных для интерпретации [values = 0].');
    }
    $resultString = 'UPDATE `' . $this->table->interpretation($driver) . '` SET ';
    foreach($this->fields as $k => $field){
      $resultString .= $field->interpretation($driver) . ' = "' . $this->values[$k] . '",';
    }
    $resultString = substr($resultString, 0, -1);
    if(!empty($this->where)){
      exceptions\InvalidArgumentException::verifyType($driver, 'Sn');
      try{
        $resultString .= ' ' . $this->where->interpretation($driver);
      }
      catch(exceptions\NotFoundDataException $exc){
        throw $exc;
      }
      catch(exceptions\InvalidArgumentException $exc){
        throw $exc;
      }
    }

    return $resultString;
  }

  /**
   * @return \PPHP\tools\patterns\database\query\Table
   */
  public function getTable(){
    return $this->table;
  }

  /**
   * @return \PPHP\tools\patterns\database\query\Where
   */
  public function getWhere(){
    return $this->where;
  }

  public function getFields(){
    return $this->fields;
  }

  public function getValues(){
    return $this->values;
  }
}
