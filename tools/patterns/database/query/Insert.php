<?php
namespace PPHP\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

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
   *
   * @param Field $field Целевое поле.
   * @param string|number|boolean $value Значение поля.
   *
   * @throws StandardException Выбрасывается в случае, если указанное поле уже присутствует в запросе.
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  public function addData(Field $field, $value){
    if($this->fields->offsetExists($field)){
      throw new StandardException('Ошибка дублирования компонента.');
    }
    if(is_object($value) || is_array($value)){
      throw new exceptions\InvalidArgumentException('Неверный тип аргумента, ожидается string, integer, float, boolean.');
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
   *
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   *
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты объекта.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver=null){
    if($this->values->count() == 0){
      throw new exceptions\NotFoundDataException();
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
}
