<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\classes\standard\baseType\String;

/**
 * Класс представляет объектную SQL инструкцию для получение записей из таблицы.
 * Объекты данного класса могут быть восстановлены из строки следующего формата: SELECT (`имяПоля`|имяТаблицы.имяПоля, ...)|* FROM `имяТаблицы`, ... [типОбъединения JOIN `имяТаблицы` ON логическоеВыражение, ...] [ORDER BY `имяПоля`|имяТаблицы.имяПоля ASC|DESC] [LIMIT числоСтрок] [WHERE (логическоеВыражение)].
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Select extends ComponentQuery{
  /**
   * @var \PPHP\tools\patterns\database\query\Field[]|\PPHP\tools\patterns\database\query\FieldAlias[] Множество запрашиваемых полей.
   */
  private $fields;

  /**
   * @var \PPHP\tools\patterns\database\query\Table[] Множество таблиц, используемых в запросе.
   */
  private $tables;

  /**
   * @var \PPHP\tools\patterns\database\query\Join[] Множество соединений.
   */
  private $joins;

  /**
   * @var \PPHP\tools\patterns\database\query\Where Условие отбора записей.
   */
  private $where;

  /**
   * @var \PPHP\tools\patterns\database\query\OrderBy Сортировка записей.
   */
  private $orderBy;

  /**
   * @var \PPHP\tools\patterns\database\query\Limit Ограничитель выборки.
   */
  private $limit;

  /**
   * @var boolean Логический флаг, свидетельствующий о том, что должны быть выбраны все поля.
   */
  private $allField;

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getMasks($driver = null){
    return ['SELECT ((?:' . self::getPatterns()['fieldGroup'] . ')|(?:\*)) FROM (' . self::getPatterns()['tableGroup'] . ')((?: ' . Join::getMasks()[0] . ')*)(?: (' . OrderBy::getMasks()[0] . '))?(?: (' . Limit::getMasks()[0] . '))?(?: (' . Where::getMasks()[0] . '))?'];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getPatterns($driver = null){
    return ['fieldGroup' => '(?:(?:' . Field::getMasks()[0] . ')|(?:' . Field::getMasks()[1] . ')|(?:' . FieldAlias::getMasks()[0] . '))(?:, ?(?:(?:' . Field::getMasks()[0] . ')|(?:' . Field::getMasks()[1] . ')))*', 'tableGroup' => '(?:`' . Table::getMasks()[0] . '`)(?:, ?`' . Table::getMasks()[0] . '`)*'];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Restorable
   */
  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    $m = parent::reestablish($string);
    $select = new self;
    if($m[1] == '*'){
      $select->addAllField();
    }
    else{
      $fields = explode(',', $m[1]);
      foreach($fields as $field){
        $field = trim($field);
        if(strpos($field, ' as ') === false){
          $select->addField(Field::reestablish($field));
        }
        else{
          $select->addAliasField(FieldAlias::reestablish($field));
        }
      }
    }
    $tables = explode(',', str_replace(' ', '', $m[2]));
    foreach($tables as $table){
      $select->addTable(Table::reestablish(substr($table, 1, -1)));
    }
    if(!empty($m[3])){
      // Разбор join компонентов на части по пробелу и склеивание их по средствам нахождения ключевых модификаторов типа join.
      // Не следует использовать ключевый модификаторы join (CROSS|INNER|LEFT|RIGHT|FULL) в условиях запроса!
      $joinComponents = explode(' ', trim($m[3]));
      $join = null;
      foreach($joinComponents as $component){
        if(preg_match('/^(' . Join::getPatterns()['types'] . ')$/u', $component)){
          if(!is_null($join)){
            $select->addJoin(Join::reestablish(trim($join)));
          }
          $join = $component . ' ';
        }
        else{
          $join .= $component . ' ';
        }
      }
      $select->addJoin(Join::reestablish(trim($join)));
    }
    if(!empty($m[4])){
      $select->insertOrderBy(OrderBy::reestablish($m[4]));
    }
    if(!empty($m[5])){
      $select->insertLimit(Limit::reestablish($m[5]));
    }
    if(!empty($m[6])){
      $select->insertWhere(Where::reestablish($m[6]));
    }

    return $select;
  }

  /**
   *
   */
  function __construct(){
    $this->fields = [];
    $this->tables = [];
    $this->joins = [];
    $this->allField = false;
  }

  /**
   * Метод добавляет поле в запрос.
   * @param \PPHP\tools\patterns\database\query\Field $field Добавляемое поле.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException Выбрасывается в случае, если указанное поле уже присутствует в запросе.
   * @return Select Метод возвращает вызываемый объект.
   */
  public function addField(Field $field){
    if(array_search($field, $this->fields) !== false){
      throw new exceptions\DuplicationException('Ошибка дублирования компонента.');
    }
    $this->fields[] = $field;

    return $this;
  }

  /**
   * Метод добавляет поле с алиасом в запрос.
   * @param \PPHP\tools\patterns\database\query\FieldAlias $field Добавляемое поле.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException Выбрасывается в случае, если указанное поле уже присутствует в запросе.
   * @return Select Метод возвращает вызываемый объект.
   */
  public function addAliasField(FieldAlias $field){
    if(array_search($field, $this->fields) !== false){
      throw new exceptions\DuplicationException('Ошибка дублирования компонента.');
    }
    $this->fields[] = $field;

    return $this;
  }

  /**
   * Метод добавляет таблицу в запрос.
   * @param \PPHP\tools\patterns\database\query\Table $table Добавляемая таблица.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException Выбрасывается в случае, если указанная таблица уже присутствует в запросе.
   * @return Select Метод возвращает вызываемый объект.
   */
  public function addTable(Table $table){
    if(array_search($table, $this->tables) !== false){
      throw new exceptions\DuplicationException('Ошибка дублирования компонента.');
    }
    $this->tables[] = $table;

    return $this;
  }

  /**
   * Метод добавляет соединение в запрос.
   * @param \PPHP\tools\patterns\database\query\Join $join Добавляемое соединение.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\DuplicationException Выбрасывается в случае, если указанное соединение уже присутствует в запросе.
   * @return Select Метод возвращает вызываемый объект.
   */
  public function addJoin(Join $join){
    if(array_search($join, $this->joins) !== false){
      throw new exceptions\DuplicationException('Ошибка дублирования компонента.');
    }
    $this->joins[] = $join;

    return $this;
  }

  /**
   * Метод устанавливает условие отбора для запроса.
   * @param \PPHP\tools\patterns\database\query\Where $where Условие отбора.
   * @return Select Метод возвращает вызываемый объект.
   */
  public function insertWhere(Where $where){
    $this->where = $where;

    return $this;
  }

  /**
   * Метод определяет порядок сортировки для запроса.
   * @param \PPHP\tools\patterns\database\query\OrderBy $orderBy Способ сортировки.
   * @return Select Метод возвращает вызываемый объект.
   */
  public function insertOrderBy(OrderBy $orderBy){
    $this->orderBy = $orderBy;

    return $this;
  }

  /**
   * Метод определяет ограничение выборки.
   * @param \PPHP\tools\patterns\database\query\Limit $limit Ограничение выборки.
   * @return Select Метод возвращает вызываемый объект.
   */
  public function insertLimit(Limit $limit){
    $this->limit = $limit;

    return $this;
  }

  /**
   * Метод устанавливает флаг отбора всех полей.
   * @return Select Метод возвращает вызываемый объект.
   */
  public function addAllField(){
    $this->allField = true;

    return $this;
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Interpreter
   */
  public function interpretation($driver = null){
    exceptions\InvalidArgumentException::verifyType($driver, 'Sn');
    if((count($this->fields) == 0 && !$this->allField) || count($this->tables) == 0){
      throw new exceptions\NotFoundDataException('Недостаточно данных для интерпретации.');
    }
    if($this->allField){
      $fieldsString = '*';
    }
    else{
      $fieldsString = '';
      foreach($this->fields as $field){
        $fieldsString .= $field->interpretation($driver) . ',';
      }
      $fieldsString = substr($fieldsString, 0, -1);
    }
    $tableString = '';
    foreach($this->tables as $table){
      $tableString .= '`' . $table->interpretation($driver) . '`,';
    }
    $tableString = substr($tableString, 0, strlen($tableString) - 1);
    try{
      $joinString = [];
      foreach($this->joins as $join){
        $joinString[] = $join->interpretation($driver);
      }
      $joinString = implode(' ', $joinString);
      $whereString = (is_object($this->where)? $this->where->interpretation($driver) : '');
      $orderByString = (is_object($this->orderBy)? $this->orderBy->interpretation($driver) : '');
    }
    catch(exceptions\NotFoundDataException $exc){
      throw $exc;
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
    // Формирование платформо-независимой выборки при отсутствии несовместимых элементов.
    if(empty($this->limit)){
      return trim('SELECT ' . $fieldsString . ' FROM ' . $tableString . ' ' . $joinString . ' ' . $whereString . ' ' . $orderByString);
    }
    // Формирования платформо-зависимой выборки при наличии несовместимых элементов.
    else{
      exceptions\InvalidArgumentException::verifyType($driver, 'S');
      // Обработка LIMIT элемента
      $limitString = $this->limit->interpretation($driver);
      $staticPartString = $fieldsString . ' FROM ' . $tableString . ' ' . $joinString . ' ' . $whereString . ' ';
      switch($driver){
        case 'sqlsrv': // MS SQL Server
        case 'firebird': // Firebird
          return trim('SELECT ' . $limitString . $staticPartString . $orderByString);
        case 'oci': // Oracle
          return trim('SELECT ' . $staticPartString . ' AND (' . $limitString . ') ' . $orderByString);
        case 'mysql': // MySQL
        case 'pgsql': // PostgreSQL
        case 'ibm': // DB2
          return trim('SELECT ' . $staticPartString . $orderByString . ' ' . $limitString);
        default:
          throw exceptions\InvalidArgumentException::getValidException('sqlsrv|firebird|oci|mysql|pgsql|ibm', $driver);
      }
    }
  }

  /**
   * Метод возвращает массив полей данного запроса или пустой массив, если выбраны все поля.
   * @return \PPHP\tools\patterns\database\query\Field[]|\PPHP\tools\patterns\database\query\FieldAlias[]
   */
  public function getFields(){
    if($this->allField){
      return [];
    }

    return $this->fields;
  }

  /**
   * @return \PPHP\tools\patterns\database\query\Join[]
   */
  public function getJoins(){
    return $this->joins;
  }

  /**
   * @return \PPHP\tools\patterns\database\query\Limit
   */
  public function getLimit(){
    return $this->limit;
  }

  /**
   * @return \PPHP\tools\patterns\database\query\OrderBy
   */
  public function getOrderBy(){
    return $this->orderBy;
  }

  /**
   * @return \PPHP\tools\patterns\database\query\Table[]
   */
  public function getTables(){
    return $this->tables;
  }

  /**
   * @return \PPHP\tools\patterns\database\query\Where
   */
  public function getWhere(){
    return $this->where;
  }

  /**
   * Определяет, производится ли выборка всех полей.
   * @return boolean true - если производится выборка всех полей, иначе - false.
   */
  public function isAllFields(){
    return $this->allField;
  }
}
