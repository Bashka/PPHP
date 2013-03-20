<?php
namespace PPHP\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

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
   *
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   *
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты объекта.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver=null){
    try{
      return 'DELETE FROM `' . $this->table->interpretation($driver) . '` ' . (is_object($this->where)? $this->where->interpretation($driver) : '');
    }
    catch(exceptions\NotFoundDataException $exc){
      throw $exc;
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }
}
