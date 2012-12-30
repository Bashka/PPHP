<?php
namespace PPHP\tools\patterns\database\query;

/**
 * Операция объединения записей.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Join implements ComponentQuery{
  const CROSS = 'CROSS';
  const INNER = 'INNER';
  const LEFT = 'LEFT';
  const RIGHT = 'RIGHT';
  const FULL = 'FULL';
  /**
   * Тип связи.
   * @var string
   */
  protected $type;
  /**
   * Связываемая таблица.
   * @var \PPHP\tools\patterns\database\query\Table
   */
  protected $table;
  /**
   * Условие связывания.
   * @var \PPHP\tools\patterns\database\query\Condition
   */
  protected $condition;

  /**
   * @param string $type Тип соединения. CROSS, INNER, LEFT, RIGHT или FULL.
   * @param Table $table Связываемая таблица.
   * @param Condition $condition Условие связывания.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct($type, \PPHP\tools\patterns\database\query\Table $table, \PPHP\tools\patterns\database\query\Condition $condition){
    if($type != 'CROSS' && $type != 'INNER' && $type != 'LEFT' && $type != 'RIGHT' && $type != 'FULL'){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException();
    }
    $this->type = $type;
    $this->table = $table;
    $this->condition = $condition;
  }

  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   * @param string|null $driver Используемая СУБД.
   * @return string Представление элемента в виде части SQL запроса.
   */
  public function interpretation($driver=null){
    return $this->type . ' JOIN `' . $this->table->interpretation() . '` ON ' . $this->condition->interpretation();
  }
}