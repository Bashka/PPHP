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
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return ['`' . self::getPatterns()['fieldName'] . '`', Table::getPatterns()['tableName'] . '\.' . self::getPatterns()['fieldName']];
  }

  /**
   * Метод возвращает массив шаблонов, описывающих различные компоненты шаблонов верификации.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getPatterns($driver = null){
    return ['fieldName' => '[A-Za-z_][A-Za-z0-9_]*'];
  }

  /**
   * Метод восстанавливает объект из строки.
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws exceptions\StructureException Выбрасывается в случае, если исходная строка не отвечает требования структуры.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return Field Результирующий объект.
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
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct($name){
    exceptions\InvalidArgumentException::verifyType($name, 'S');
    $this->name = $name;
  }

  /**
   * Метод определяет таблицу, к которой относится данное поле.
   * @param Table $table Таблица, к которой будет относится поле.
   * @return $this Метод возвращает вызываемый объект.
   */
  public function setTable(Table $table){
    $this->table = $table;

    return $this;
  }

  /**
   * Метод возвращает представление элемента в виде части SQL запроса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходного объекта.
   * @return string Результат интерпретации.
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
   * @return Table
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
