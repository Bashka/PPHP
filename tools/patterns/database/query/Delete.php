<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет объектную SQL инструкцию для удаления записей из таблицы.
 * Объекты данного класса могут быть восстановлены из строки следующего формата: DELETE FROM `имяТаблицы` [WHERE (логическоеВыражение)].
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Delete extends ComponentQuery{
  /**
   * @var \PPHP\tools\patterns\database\query\Table Целевая таблица.
   */
  private $table;

  /**
   * @var \PPHP\tools\patterns\database\query\Where Условие отбора.
   */
  private $where;

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getMasks($driver = null){
    return ['DELETE FROM `(' . Table::getMasks()[0] . ')`( ' . Where::getMasks()[0] . ')?'];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Restorable
   */
  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    /**
     * @var string $mask
     */
    $mask = parent::reestablish($string);
    $o = new Delete(Table::reestablish($mask[1]));
    $o->insertWhere(Where::reestablish(trim($mask[2])));

    return $o;
  }

  /**
   * @param \PPHP\tools\patterns\database\query\Table $table Целевая таблица.
   */
  function __construct(Table $table){
    $this->table = $table;
  }

  /**
   * Метод устанавливает условие отбора для запроса.
   * @param \PPHP\tools\patterns\database\query\Where $where Условие отбора.
   * @return \PPHP\tools\patterns\database\query\Delete Метод возвращает вызываемый объект.
   */
  public function insertWhere(Where $where){
    $this->where = $where;

    return $this;
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Interpreter
   */
  public function interpretation($driver = null){
    exceptions\InvalidArgumentException::verifyType($driver, 'Sn');
    try{
      return 'DELETE FROM `' . $this->table->interpretation($driver) . '`' . (is_object($this->where)? ' ' . $this->where->interpretation($driver) : '');
    }
    catch(exceptions\NotFoundDataException $exc){
      throw $exc;
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * @return \PPHP\tools\patterns\database\query\Table
   */
  public function getTable(){
    return $this->table;
  }

  /**
   * @return \PPHP\tools\patterns\database\query\Where
   */
  public function getWhere(){
    return $this->where;
  }
}
