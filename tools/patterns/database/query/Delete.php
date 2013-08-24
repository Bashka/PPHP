<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет SQL запрос для удаления записей из таблицы.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Delete extends ComponentQuery{
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
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return ['DELETE FROM `(' . Table::getMasks()[0] . ')`( ' . Where::getMasks()[0] . ')?'];
  }

  /**
   * Метод восстанавливает объект из строки.
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws exceptions\StructureException Выбрасывается в случае, если исходная строка не отвечает требования структуры.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return static Результирующий объект.
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
   * @param Table $table Целевая таблица.
   */
  function __construct(Table $table){
    $this->table = $table;
  }

  /**
   * Метод устанавливает условие отбора для запроса.
   * @param Where $where Условие отбора.
   * @return $this Метод возвращает вызываемый объект.
   */
  public function insertWhere(Where $where){
    $this->where = $where;

    return $this;
  }

  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае, если отсутствуют обязательные компоненты объекта.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return string Результат интерпретации.
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
   * @return Table
   */
  public function getTable(){
    return $this->table;
  }

  /**
   * @return Where
   */
  public function getWhere(){
    return $this->where;
  }
}
