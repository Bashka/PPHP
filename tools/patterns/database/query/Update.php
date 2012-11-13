<?php
namespace PPHP\tools\patterns\database\query;

/**
 * Класс представляет SQL запрос для обновления данных в БД.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Update implements ComponentQuery{
  /**
   * Целевая таблица.
   * @var Table
   */
  private $table;
  /**
   * Множество полей, используемых в таблице.
   * @var \SplObjectStorage
   */
  private $fields;
  /**
   * Множество значений, устанавливаемых в поля записи.
   * @var \SplQueue
   */
  private $values;
  /**
   * Условие отбора.
   * @var Where
   */
  private $where;

  /**
   * @param Table $table Целевая таблица.
   */
  function __construct(Table $table){
    $this->fields = new \SplObjectStorage();
    $this->values = new \SplQueue();
    $this->table = $table;
  }

  /**
   * Метод устанавливает условие отбора для запроса.
   * @param Where $where Условие отбора.
   */
  public function insertWhere(Where $where){
    $this->where = $where;
  }

  /**
   * Метод добавляет данные в запрос.
   * @param Field $field Целевое поле.
   * @param string|number|boolean $value Значение целевого поля.
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
   * Метод возвращает представление элемента в виде части SQL запроса.
   * @param string|null $driver Используемая СУБД.
   * @throws StandardException Выбрасывается в случае, если отсутствуют обязательные компоненты запроса.
   * @return string Представление элемента в виде части SQL запроса.
   */
  public function interpretation($driver=null){
    if($this->values->count() == 0){
      throw new StandardException();
    }
    $resultString = 'UPDATE `' . $this->table->interpretation() . '` SET ';
    $this->fields->rewind();
    $this->values->rewind();
    do{
      $resultString .= $this->fields->current()->interpretation() . ' = "' . $this->values->current() . '",';
      $this->fields->next();
      $this->values->next();
    }
    while($this->values->valid());
    $resultString = substr($resultString, 0, strlen($resultString) - 1);
    if(!empty($this->where)){
      $resultString .= ' ' . $this->where->interpretation();
    }
    return $resultString;
  }

}
