<?php
namespace PPHP\tools\patterns\database\query;

/**
 * Класс представляет SQL запрос для удаления записей из таблицы.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Delete implements ComponentQuery{
  /**
   * Целевая таблица.
   * @var Table
   */
  private $table;
  /**
   * Условие отбора.
   * @var Where
   */
  private $where;

  /**
   * @param Table $table Целевая таблица.
   */
  function __construct(Table $table){
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
   * Метод возвращает представление элемента в виде части SQL запроса.
   * @param string|null $driver Используемая СУБД.
   * @return string Представление элемента в виде части SQL запроса.
   */
  public function interpretation($driver=null){
    return 'DELETE FROM `' . $this->table->interpretation() . '` ' . (is_object($this->where)? $this->where->interpretation() : '');
  }


}
