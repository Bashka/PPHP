<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс представляет условие в SQL запросе.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class Where extends ComponentQuery{
  /**
   * @var \PPHP\tools\patterns\database\query\Condition Логическая операция.
   */
  private $condition;

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getMasks($driver = null){
    return ['WHERE (?:' . Condition::getPatterns()['condition'] . ')'];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Restorable
   */
  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    parent::reestablish($string);

    return new self(Condition::reestablishCondition(substr($string, 6)));
  }

  /**
   * @param \PPHP\tools\patterns\database\query\Condition $condition Логическая операция.
   */
  function __construct(Condition $condition){
    $this->condition = $condition;
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Interpreter
   */
  public function interpretation($driver = null){
    exceptions\InvalidArgumentException::verifyType($driver, 'Sn');
    try{
      return 'WHERE ' . $this->condition->interpretation($driver);
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
}
