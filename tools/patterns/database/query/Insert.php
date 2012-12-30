<?php
namespace PPHP\tools\patterns\database\query;

/**
 * Класс представляет SQL запрос для вставки записи в таблицу.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Insert implements ComponentQuery{
  /**
   * Целевая таблица.
   * @var Table
   */
  private $table;
  /**
   * Множество используемых в запросе полей таблицы.
   * @var \SplObjectStorage
   */
  private $fields;
  /**
   * Множество устанавливаемых значений записи.
   * @var \SplObjectStorage
   */
  private $values;

  /**
   * SQL инструкция, возвращающая добавляемое множество значений записи(ей).
   * @var Select
   */
  private $select;

  /**
   * @param Table $table Целевая таблица.
   */
  function __construct(Table $table){
    $this->fields = new \SplObjectStorage();
    $this->values = new \SplQueue();
    $this->table = $table;
  }

  /**
   * Метод добавляет данные в запрос.
   * @param Field $field Целевое поле.
   * @param string|number|boolean $value Значение поля.
   * @throws StandardException Выбрасывается в случае, если указанное поле уже присутствует в запросе.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  public function addData(Field $field, $value){
    if($this->fields->offsetExists($field)){
      throw new StandardException('Ошибка дублирования компонента.');
    }
    if(is_object($value) || is_array($value)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException();
    }
    $this->fields->attach($field);
    $this->values->enqueue($value);
  }

  /**
   * Метод устанавливает запрос, возвращающий данные для добавления.
   * @param Select $select SELECT запрос, возвращающий данные для добавления.
   */
  public function setSelect(Select $select){
    $this->select = $select;
  }

  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   * @param string|null $driver Используемая СУБД.
   * @throws StandardException Выбрасывается в случае, если отсутствуют обязательные компоненты запроса.
   * @return string Представление элемента в виде части SQL запроса.
   */
  public function interpretation($driver=null){
    if($this->values->count() == 0){
      throw new StandardException();
    }

    $resultString = 'INSERT INTO `' . $this->table->interpretation() . '` (';
    foreach($this->fields as $field){
      $resultString .= $field->interpretation() . ',';
    }
    $resultString = substr($resultString, 0, strlen($resultString) - 1);

    // Генерация запроса с данными вложенного запроса
    if(is_object($this->select)){
      $resultString = ') (' . $this->select->interpretation() . ')';
      return $resultString;
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
}
