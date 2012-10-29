<?php
namespace PPHP\tools\patterns\database\query;

/**
 * Класс представляет поле таблицы в запросе.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Field implements ComponentQuery{
  /**
   * Имя поля.
   * @var string
   */
  private $name;
  /**
   * Таблица, к которой относится поле.
   * @var Table
   */
  private $table;

  /**
   * @param string $name Имя поля.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct($name){
    if(!is_string($name) || empty($name)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('string', $name);
    }
    $this->name = $name;
  }

  /**
   * Метод определяет таблицу, к которой относится данное поле.
   * @param Table $table Таблица, к которой будет относится поле.
   */
  public function setTable(Table $table){
    $this->table = $table;
  }

  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   * @param string|null $driver Используемая СУБД.
   * @return string Представление элемента в виде части SQL запроса.
   */
  public function interpretation($driver=null){
    if(!empty($this->table)){
      return $this->table->interpretation() . '.' . $this->name;
    }
    else{
      return '`' . $this->name . '`';
    }
  }
}
