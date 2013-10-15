<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\classes\standard\baseType\String;

/**
 * Класс представляет компонент объединения записей.
 * Объекты данного класса могут быть восстановлены из строки следующего формата: CROSS|INNER|LEFT|RIGHT|FULL JOIN  `имяТаблицы` ON (`имяПоля`|имяТаблицы.имяПоля = `имяПоля`|имяТаблицы.имяПоля)|(`имяПоля`|имяТаблицы.имяПоля = "значение").
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
   * @var string Тип связи.
   */
  protected $type;

  /**
   * @var \PPHP\tools\patterns\database\query\Table Связываемая таблица.
   */
  protected $table;

  /**
   * @var \PPHP\tools\patterns\database\query\Condition Условие связывания.
   */
  protected $condition;

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getMasks($driver = null){
    // Условие объединения ограничено одним логическим выражением.
    return [self::getPatterns()['types'] . ' JOIN `(?:' . Table::getMasks()[0] . ')` ON (?:(?:' . LogicOperation::getMasks()[0] . ')|(?:' . LogicOperation::getMasks()[1] . '))'];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getPatterns($driver = null){
    return ['types' => '(?:(?:' . self::CROSS . ')|(?:' . self::INNER . ')|(?:' . self::LEFT . ')|(?:' . self::RIGHT . ')|(?:' . self::FULL . '))'];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Restorable
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
   * @param \PPHP\tools\patterns\database\query\Table $table Связываемая таблица.
   * @param \PPHP\tools\patterns\database\query\Condition $condition Условие связывания.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
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
   * @prototype \PPHP\tools\patterns\interpreter\Interpreter
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
   * @return \PPHP\tools\patterns\database\query\Condition
   */
  public function getCondition(){
    return $this->condition;
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
  public function getType(){
    return $this->type;
  }
}