<?php
namespace PPHP\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет SQL запрос для получение записей из таблицы.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Select implements ComponentQuery{
  /**
   * Множество запрашиваемых полей.
   * @var \SplObjectStorage
   */
  private $fields;
  /**
   * Множество таблиц, используемых в запросе.
   * @var \SplObjectStorage
   */
  private $tables;

  /**
   * Множество соединений
   * @var \SplObjectStorage
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

  function __construct(){
    $this->fields = new \SplObjectStorage();
    $this->tables = new \SplObjectStorage();
    $this->joins = new \SplObjectStorage();
    $this->allField = false;
  }

  /**
   * Метод добавляет поле в запрос.
   *
   * @param Field $field Добавляемое поле.
   *
   * @throws StandardException Выбрасывается в случае, если указанное поле уже присутствует в запросе.
   */
  public function addField(Field $field){
    if($this->fields->offsetExists($field)){
      throw new StandardException('Ошибка дублирования компонента.');
    }
    $this->fields->attach($field);
  }

  /**
   * Метод добавляет поле с алиасом в запрос.
   *
   * @param FieldAlias $field Добавляемое поле.
   *
   * @throws StandardException Выбрасывается в случае, если указанное поле уже присутствует в запросе.
   */
  public function addAliasField(FieldAlias $field){
    if($this->fields->offsetExists($field)){
      throw new StandardException('Ошибка дублирования компонента.');
    }
    $this->fields->attach($field);
  }

  /**
   * Метод добавляет таблицу в запрос.
   *
   * @param Table $table Добавляемая таблица.
   *
   * @throws StandardException Выбрасывается в случае, если указанная таблица уже присутствует в запросе.
   */
  public function addTable(Table $table){
    if($this->tables->offsetExists($table)){
      throw new StandardException('Ошибка дублирования компонента.');
    }
    $this->tables->attach($table);
  }

  /**
   * Метод добавляет соединение в запрос.
   *
   * @param Join $join Добавляемое соединение.
   *
   * @throws StandardException Выбрасывается в случае, если указанное соединение уже присутствует в запросе.
*/
  public function addJoin(Join $join){
    if($this->joins->offsetExists($join)){
      throw new StandardException('Ошибка дублирования компонента.');
    }
    $this->joins->attach($join);
  }

  /**
   * Метод устанавливает условие отбора для запроса.
   * @param Where $where Условие отбора.
   */
  public function insertWhere(Where $where){
    $this->where = $where;
  }

  /**
   * Метод определяет порядок сортировки для запроса.
   * @param OrderBy $orderBy Способ сортировки.
   */
  public function insertOrderBy(OrderBy $orderBy){
    $this->orderBy = $orderBy;
  }

  /**
   * Метод определяет ограничение выборки.
   * @param Limit $limit Ограничение выборки.
   */
  public function insertLimit(Limit $limit){
    $this->limit = $limit;
  }

  /**
   * Метод устанавливает флаг отбора всех полей.
   */
  public function addAllField(){
    $this->allField = true;
  }

  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   *
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   *
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты объекта.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver = null){
    if(($this->fields->count() == 0 && !$this->allField) || $this->tables->count() == 0){
      throw new exceptions\NotFoundDataException();
    }

    try{
      if($this->allField){
        $fieldsString = '*';
      }
      else{
        $fieldsString = '';
        foreach($this->fields as $field){
          $fieldsString .= $field->interpretation($driver) . ',';
        }
        $fieldsString = substr($fieldsString, 0, strlen($fieldsString) - 1);
      }

      $tableString = '';
      foreach($this->tables as $table){
        $tableString .= '`' . $table->interpretation($driver) . '`,';
      }
      $tableString = substr($tableString, 0, strlen($tableString) - 1);

      $joinString = '';
      foreach($this->joins as $join){
        $joinString .= $join->interpretation($driver);
      }

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
      if(!is_string($driver) || empty($driver)){
        throw new exceptions\InvalidArgumentException('string', $driver);
      }
      // { Обработка LIMIT элемента
      $limitString = $this->limit->interpretation($driver);
      $staticPartString = ' ' . $fieldsString . ' FROM ' . $tableString . ' ' . $joinString . ' ' . $whereString . ' ';

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
          throw new exceptions\InvalidArgumentException('Недопустимое значение параметра. Ожидается sqlsrv, firebird, oci, mysql, pgsql или ibm.');
      }
      // }
    }
  }
}
