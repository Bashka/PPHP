<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет SQL запрос для обновления данных в БД.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Update extends ComponentQuery{
  /**
   * Целевая таблица.
   * @var Table
   */
  private $table;

  /**
   * Множество полей, используемых в таблице.
   * @var Field[]
   */
  private $fields;

  /**
   * Множество значений, устанавливаемых в поля записи.
   * @var mixed[]
   */
  private $values;

  /**
   * Условие отбора.
   * @var Where
   */
  private $where;

  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return ['UPDATE `(' . Table::getPatterns()['tableName'] . ')` SET (' . self::getPatterns()['setValue'] . '(, ?' . self::getPatterns()['setValue'] . ')*)( ' . Where::getMasks()[0] . ')?'];
  }

  /**
   * Метод возвращает массив шаблонов, описывающих различные компоненты шаблонов верификации.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getPatterns($driver = null){
    return ['setValue' => '(' . Field::getMasks()[0] . '|' . Field::getMasks()[1] . ') = ' . LogicOperation::getPatterns()['stringValue']];
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
   * @param Table $table Целевая таблица.
   */
  function __construct(Table $table){
    $this->fields = [];
    $this->values = [];
    $this->table = $table;
  }

  /**
   * Метод устанавливает условие отбора для запроса.
   * @param Where $where Условие отбора.
   * @return $this Метод возвращает вызываемый объект.
   */
  public function insertWhere(Where $where){
    $this->where = $where;

    return $this;
  }

  /**
   * Метод добавляет данные в запрос.
   * @param Field $field Целевое поле.
   * @param string|number|boolean $value Значение целевого поля.
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
   * Метод возвращает представление элемента в виде части SQL запроса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты объекта.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Результат интерпретации.
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
   * @return Table
   */
  public function getTable(){
    return $this->table;
  }

  /**
   * @return Where
   */
  public function getWhere(){
    return $this->where;
  }
}
