<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\classes\standard\baseType\String;

/**
 * Операция объединения записей.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Join extends ComponentQuery{
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
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    // Условие объединения ограничено одним логическим выражением.
    return [self::getPatterns()['types'] . ' JOIN `(?:' . Table::getMasks()[0] . ')` ON (?:(?:' . LogicOperation::getMasks()[0] . ')|(?:' . LogicOperation::getMasks()[1] . '))'];
  }

  /**
   * Метод возвращает массив шаблонов, описывающих различные компоненты шаблонов верификации.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getPatterns($driver = null){
    return ['types' => '(?:(?:' . self::CROSS . ')|(?:' . self::INNER . ')|(?:' . self::LEFT . ')|(?:' . self::RIGHT . ')|(?:' . self::FULL . '))'];
  }

  /**
   * Метод восстанавливает объект из строки.
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws exceptions\StructureException Выбрасывается в случае, если исходная строка не отвечает требования структуры.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return Join Результирующий объект.
   */
  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    parent::reestablish($string);
    $s = new String($string);
    $type = $s->nextComponent(' ')->getVal();
    $s->setPoint($s->getPoint() + 5);
    $table = substr($s->nextComponent(' ON ')->getVal(), 1, -1);
    $condition = $s->sub()->getVal();

    return new static($type, Table::reestablish($table), Condition::reestablishCondition($condition));
  }

  /**
   * @param string $type Тип соединения. CROSS, INNER, LEFT, RIGHT или FULL.
   * @param Table $table Связываемая таблица.
   * @param Condition $condition Условие связывания.
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct($type, Table $table, Condition $condition){
    if($type != 'CROSS' && $type != 'INNER' && $type != 'LEFT' && $type != 'RIGHT' && $type != 'FULL'){
      throw exceptions\InvalidArgumentException::getValidException('CROSS|INNER|LEFT|RIGHT|FULL', $type);
    }
    $this->type = $type;
    $this->table = $table;
    $this->condition = $condition;
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
      return $this->type . ' JOIN `' . $this->table->interpretation($driver) . '` ON ' . $this->condition->interpretation($driver);
    }
    catch(exceptions\NotFoundDataException $exc){
      throw $exc;
    }
    catch(exceptions\InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * @return Condition
   */
  public function getCondition(){
    return $this->condition;
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
  public function getType(){
    return $this->type;
  }
}