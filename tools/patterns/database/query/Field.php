<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет поле таблицы в запросе.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Field extends ComponentQuery{
  /**
   * @var string Имя поля.
   */
  private $name;

  /**
   * @var \PPHP\tools\patterns\database\query\Table Таблица, к которой относится поле.
   */
  private $table;

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getMasks($driver = null){
    return ['`' . self::getPatterns()['fieldName'] . '`', Table::getPatterns()['tableName'] . '\.' . self::getPatterns()['fieldName']];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getPatterns($driver = null){
    return ['fieldName' => '[A-Za-z_][A-Za-z0-9_]*'];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Restorable
   */
  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    $mask = parent::reestablish($string);
    $object = null;
    if($mask['key'] == 0){
      $object = new static(substr(substr($string, 0, -1), 1));
    }
    elseif($mask['key'] == 1){
      $components = explode('.', $string);
      /**
       * @var Field $object
       */
      $object = new static($components[1]);
      $object->setTable(Table::reestablish($components[0]));
    }

    return $object;
  }

  /**
   * @param string $name Имя поля.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct($name){
    exceptions\InvalidArgumentException::verifyType($name, 'S');
    $this->name = $name;
  }

  /**
   * Метод определяет таблицу, к которой относится данное поле.
   * @param \PPHP\tools\patterns\database\query\Table $table Таблица, к которой будет относится поле.
   * @return \PPHP\tools\patterns\database\query\Field Метод возвращает вызываемый объект.
   */
  public function setTable(Table $table){
    $this->table = $table;

    return $this;
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Interpreter
   */
  public function interpretation($driver = null){
    if(!empty($this->table)){
      return $this->table->interpretation($driver) . '.' . $this->name;
    }
    else{
      return '`' . $this->name . '`';
    }
  }

  /**
   * @return \PPHP\tools\patterns\database\query\Table
   */
  public function getTable(){
    return $this->table;
  }

  /**
   * @return string
   */
  public function getName(){
    return $this->name;
  }
}
