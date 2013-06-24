<?php
namespace PPHP\tools\classes\standard\storage\database\gateway;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\database\query as query;

/**
 * Класс представляет шлюз для работы с таблицами в базе данных через объектный интерфейс.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\storage\database\gateway
 */
class Table{
  /**
   * Используемая для работы таблица.
   * @var query\Table
   */
  protected $table;

  /**
   * @param string $table Имя рабочей таблицы.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа или недопустимой структуры.
   */
  function __construct($table){
    try{
      $this->table = query\Table::reestablish($table);
    }
    catch(exceptions\Exception $e){
      throw new exceptions\InvalidArgumentException('Недопустимое значение аргумента. Ожидается соответствие требованиям интерпретации классом Table вместо [' . $table . '].', 1, $e);
    }
  }

  /**
   * Метод возвращает одну запись из таблицы на основании условия отбора или возвращает пустую запись для добавления новой записи в таблицу.
   * @param integer|array|null $condition [optional] Условие отбора в виде:
   * 1. Целого числа, обозначающего идентификатор записи;
   * 2. Ассоциативного массива, ключи которого указывают имена полей, а значения - требуемые значения этих полей;
   * 3. null, обозначающего потребность в пустой записи для добавления.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return Row|null Результат запроса или null если запись не найдена.
   */
  public function getRow($condition = null){
    if(is_null($condition)){
      return new Row($this);
    }
    elseif(is_integer($condition)){
    }
    elseif(is_array($condition)){
    }
    else{
      throw exceptions\InvalidArgumentException::getTypeException(['array', 'integer', 'null'], gettype($condition));
    }
  }

  /**
   * Метод возвращает массив записей из таблицы на основании условия отбора.
   * @param query\Where|array $condition Условие отбора в виде объекта query\Where или массива, имеющего следующую структуру: [[имяПоля, оператор, значение], ...]
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return Row[] Результат запроса или пустой массив, если записей не найдено.
   */
  public function select($condition){
  }

  /**
   * @return query\Table
   */
  public function getTable(){
    return $this->table;
  }

  /**
   * Метод возвращает имя рабочей таблицы.
   * @return string
   */
  public function getTableName(){
    return $this->table->getTableName();
  }
}