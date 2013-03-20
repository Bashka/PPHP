<?php
namespace PPHP\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

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
   * @var Table
   */
  protected $table;
  /**
   * Условие связывания.
   * @var Condition
   */
  protected $condition;

  /**
   * @param string $type Тип соединения. CROSS, INNER, LEFT, RIGHT или FULL.
   * @param Table $table Связываемая таблица.
   * @param Condition $condition Условие связывания.
   *
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct($type, Table $table, Condition $condition){
    if($type != 'CROSS' && $type != 'INNER' && $type != 'LEFT' && $type != 'RIGHT' && $type != 'FULL'){
      throw new exceptions\InvalidArgumentException('Недопустимое значение параметра. Ожидается CROSS, INNER, LEFT, RIGHT или FULL.');
    }
    $this->type = $type;
    $this->table = $table;
    $this->condition = $condition;
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
    try{
      return $this->type . ' JOIN `' . $this->table->interpretation($driver) . '` ON ' . $this->condition->interpretation($driver);
    }
    catch(exceptions\NotFoundDataException $exc){
      throw $exc;
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }
}