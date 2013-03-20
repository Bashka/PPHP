<?php
namespace PPHP\tools\patterns\database\query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;

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
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct($name){
    if(!is_string($name) || empty($name)){
      throw new exceptions\InvalidArgumentException('string', $name);
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
   *
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   *
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты объекта.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Результат интерпретации.
   */
  public function interpretation($driver=null){
    if(!empty($this->table)){
      try{
        return $this->table->interpretation($driver) . '.' . $this->name;
      }
      catch(exceptions\NotFoundDataException $exc){
        throw $exc;
      }
      catch(exceptions\InvalidArgumentException $exc){
        throw $exc;
      }
    }
    else{
      return '`' . $this->name . '`';
    }
  }
}
