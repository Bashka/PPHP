<?php
namespace PPHP\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет SQL запрос для вставки записи в таблицу.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Insert extends ComponentQuery{
  /**
   * Целевая таблица.
   * @var Table
   */
  private $table;

  /**
   * Множество используемых в запросе полей таблицы.
   * @var Field[]
   */
  private $fields;

  /**
   * Множество устанавливаемых значений записи.
   * @var mixed[]
   */
  private $values;

  /**
   * SQL инструкция, возвращающая добавляемое множество значений записи(ей).
   * @var Select
   */
  private $select;

  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return ['INSERT INTO `(' . Table::getPatterns()['tableName'] . ')` (\((' . Field::getMasks()[0] . '|' . Field::getMasks()[1] . ')(, ?(' . Field::getMasks()[0] . '|' . Field::getMasks()[1] . '))*\) VALUES \(' . LogicOperation::getPatterns()['stringValue'] . '(, ?' . LogicOperation::getPatterns()['stringValue'] . ')*\))'];
  }

  /**
   * Метод восстанавливает объект из строки.
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws exceptions\StructureException Выбрасывается в случае, если исходная строка не отвечает требования структуры.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return static Результирующий объект.
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
   * @param Table $table Целевая таблица.
   */
  function __construct(Table $table){
    $this->fields = [];
    $this->values = [];
    $this->table = $table;
  }

  /**
   * Метод добавляет данные в запрос.
   * @param Field $field Целевое поле.
   * @param string|number|boolean $value Значение поля.
   * @throws exceptions\DuplicationException Выбрасывается в случае, если указанное поле уже присутствует в запросе.
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   * @return $this Метод возвращает вызываемый объект.
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
   * @param Select $select SELECT запрос, возвращающий данные для добавления.
   * @return $this Метод возвращает вызываемый объект.
   */
  public function setSelect(Select $select){
    $this->select = $select;
    return $this;
  }

  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты объекта.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver = null){
    exceptions\InvalidArgumentException::verifyType($driver, 'Sn');
    if(count($this->values) == 0 && !is_object($this->select)){
      throw new exceptions\NotFoundDataException('Нет данных для формирования строки.');
    }

    try{
      $resultString = 'INSERT INTO `' . $this->table->interpretation($driver) . '` (';
      foreach($this->fields as $field){
        $resultString .= $field->interpretation($driver) . ',';
      }
      $resultString = substr($resultString, 0, strlen($resultString) - 1);

      // Генерация запроса с данными вложенного запроса
      if(is_object($this->select)){
        $resultString = ') (' . $this->select->interpretation($driver) . ')';
        return $resultString;
      }
    }
    catch(exceptions\NotFoundDataException $exc){
      throw $exc;
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }

    // Генерация запроса с константными данными
    $resultString .= ') VALUES (';
    foreach($this->values as $val){
      $resultString .= '"' . $val . '",';
    }
    $resultString = substr($resultString, 0, strlen($resultString) - 1);
    $resultString .= ')';
    return $resultString;
  }

  /**
   * @return \PPHP\tools\patterns\database\query\Table
   */
  public function getTable(){
    return $this->table;
  }
}
