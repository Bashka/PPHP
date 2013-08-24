<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\classes\standard\baseType\String;

/**
 * Класс представляет SQL запрос для получение записей из таблицы.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Select extends ComponentQuery{
  /**
   * Множество запрашиваемых полей.
   * @var Field[]|FieldAlias[]
   */
  private $fields;

  /**
   * Множество таблиц, используемых в запросе.
   * @var Table[]
   */
  private $tables;

  /**
   * Множество соединений.
   * @var Join[]
   */
  private $joins;

  /**
   * Условие отбора записей.
   * @var Where
   */
  private $where;

  /**
   * Сортировка записей.
   * @var OrderBy
   */
  private $orderBy;

  /**
   * Ограничитель выборки.
   * @var Limit
   */
  private $limit;

  /**
   * Логический флаг, свидетельствующий о том, что должны быть выбраны все поля.
   * @var boolean
   */
  private $allField;

  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return ['SELECT ((?:' . self::getPatterns()['fieldGroup'] . ')|(?:\*)) FROM (' . self::getPatterns()['tableGroup'] . ')((?: ' . Join::getMasks()[0] . ')*)(?: (' . OrderBy::getMasks()[0] . '))?(?: (' . Limit::getMasks()[0] . '))?(?: (' . Where::getMasks()[0] . '))?'];
  }

  /**
   * Метод возвращает массив шаблонов, описывающих различные компоненты шаблонов верификации.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getPatterns($driver = null){
    return ['fieldGroup' => '(?:(?:' . Field::getMasks()[0] . ')|(?:' . Field::getMasks()[1] . ')|(?:' . FieldAlias::getMasks()[0] . '))(?:, ?(?:(?:' . Field::getMasks()[0] . ')|(?:' . Field::getMasks()[1] . ')))*', 'tableGroup' => '(?:`' . Table::getMasks()[0] . '`)(?:, ?`' . Table::getMasks()[0] . '`)*'];
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

  function __construct(){
    $this->fields = [];
    $this->tables = [];
    $this->joins = [];
    $this->allField = false;
  }

  /**
   * Метод добавляет поле в запрос.
   * @param Field $field Добавляемое поле.
   * @throws exceptions\DuplicationException Выбрасывается в случае, если указанное поле уже присутствует в запросе.
   * @return $this Метод возвращает вызываемый объект.
   */
  public function addField(Field $field){
    if(array_search($field, $this->fields)){
      throw new exceptions\DuplicationException('Ошибка дублирования компонента.');
    }
    $this->fields[] = $field;

    return $this;
  }

  /**
   * Метод добавляет поле с алиасом в запрос.
   * @param FieldAlias $field Добавляемое поле.
   * @throws exceptions\DuplicationException Выбрасывается в случае, если указанное поле уже присутствует в запросе.
   * @return $this Метод возвращает вызываемый объект.
   */
  public function addAliasField(FieldAlias $field){
    if(array_search($field, $this->fields)){
      throw new exceptions\DuplicationException('Ошибка дублирования компонента.');
    }
    $this->fields[] = $field;

    return $this;
  }

  /**
   * Метод добавляет таблицу в запрос.
   * @param Table $table Добавляемая таблица.
   * @throws exceptions\DuplicationException Выбрасывается в случае, если указанная таблица уже присутствует в запросе.
   * @return $this Метод возвращает вызываемый объект.
   */
  public function addTable(Table $table){
    if(array_search($table, $this->tables)){
      throw new exceptions\DuplicationException('Ошибка дублирования компонента.');
    }
    $this->tables[] = $table;

    return $this;
  }

  /**
   * Метод добавляет соединение в запрос.
   * @param Join $join Добавляемое соединение.
   * @throws exceptions\DuplicationException Выбрасывается в случае, если указанное соединение уже присутствует в запросе.
   * @return $this Метод возвращает вызываемый объект.
   */
  public function addJoin(Join $join){
    if(array_search($join, $this->joins)){
      throw new exceptions\DuplicationException('Ошибка дублирования компонента.');
    }
    $this->joins[] = $join;

    return $this;
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
   * Метод определяет порядок сортировки для запроса.
   * @param OrderBy $orderBy Способ сортировки.
   * @return $this Метод возвращает вызываемый объект.
   */
  public function insertOrderBy(OrderBy $orderBy){
    $this->orderBy = $orderBy;

    return $this;
  }

  /**
   * Метод определяет ограничение выборки.
   * @param Limit $limit Ограничение выборки.
   * @return $this Метод возвращает вызываемый объект.
   */
  public function insertLimit(Limit $limit){
    $this->limit = $limit;

    return $this;
  }

  /**
   * Метод устанавливает флаг отбора всех полей.
   * @return $this Метод возвращает вызываемый объект.
   */
  public function addAllField(){
    $this->allField = true;

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
   * @return Field[]|FieldAlias[]
   */
  public function getFields(){
    if($this->allField){
      return [];
    }

    return $this->fields;
  }

  public function getJoins(){
    return $this->joins;
  }

  /**
   * @return Limit
   */
  public function getLimit(){
    return $this->limit;
  }

  /**
   * @return OrderBy
   */
  public function getOrderBy(){
    return $this->orderBy;
  }

  /**
   * @return Table[]
   */
  public function getTables(){
    return $this->tables;
  }

  /**
   * @return Where
   */
  public function getWhere(){
    return $this->where;
  }
}
