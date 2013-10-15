<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет таблицу в запросе.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Table extends ComponentQuery{
  /**
   * @var string Имя таблицы.
   */
  private $tableName;

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getMasks($driver = null){
    return [self::getPatterns()['tableName']];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getPatterns($driver = null){
    return ['tableName' => '[A-Za-z_][A-Za-z0-9_]*'];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Restorable
   */
  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    parent::reestablish($string);

    return new static($string);
  }

  /**
   * @param string $tableName Имя таблицы.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct($tableName){
    exceptions\InvalidArgumentException::verifyType($tableName, 'S');
    $this->tableName = $tableName;
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Interpreter
   */
  public function interpretation($driver = null){
    return $this->tableName;
  }

  /**
   * @return string
   */
  public function getTableName(){
    return $this->tableName;
  }
}
