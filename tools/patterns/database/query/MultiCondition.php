<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException;

/**
 * Множественное логическое выражение.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class MultiCondition extends Condition{
  /**
   * @var \PPHP\tools\patterns\database\query\Condition Левый логический операнд.
   */
  private $leftOperand;

  /**
   * @var \PPHP\tools\patterns\database\query\Condition Правый логический операнд.
   */
  private $rightOperand;

  /**
   * @var string Логический оператор. Одно из следующих значений: AND, OR.
   */
  private $logicOperator;

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getMasks($driver = null){
    return ['\((' . Condition::getPatterns()['condition'] . ') ' . self::getPatterns()['operator'] . ' (' . Condition::getPatterns()['condition'] . ')\)'];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getPatterns($driver = null){
    return ['operator' => '(AND|OR)'];
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Restorable
   */
  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    $mask = parent::reestablish($string);

    return new self(Condition::reestablishCondition($mask[1]), $mask[2], Condition::reestablishCondition($mask[3]));
  }

  /**
   * @param \PPHP\tools\patterns\database\query\Condition $leftOperand Левый логический операнд.
   * @param string $logicOperator Логический оператор. Одно из следующих значений: AND, OR.
   * @param \PPHP\tools\patterns\database\query\Condition $rightOperand Правый логический операнд.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct(Condition $leftOperand, $logicOperator, Condition $rightOperand){
    InvalidArgumentException::verifyVal($logicOperator, 's # AND|OR');
    $this->leftOperand = $leftOperand;
    $this->logicOperator = $logicOperator;
    $this->rightOperand = $rightOperand;
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\Interpreter
   */
  public function interpretation($driver = null){
    InvalidArgumentException::verifyType($driver, 'Sn');
    try{
      return '(' . $this->leftOperand->interpretation($driver) . ' ' . $this->logicOperator . ' ' . $this->rightOperand->interpretation($driver) . ')';
    }
    catch(NotFoundDataException $exc){
      throw $exc;
    }
    catch(InvalidArgumentException $exc){
      throw $exc;
    }
  }

  /**
   * @return \PPHP\tools\patterns\database\query\Condition
   */
  public function getLeftOperand(){
    return $this->leftOperand;
  }

  /**
   * @return string
   */
  public function getLogicOperator(){
    return $this->logicOperator;
  }

  /**
   * @return \PPHP\tools\patterns\database\query\Condition
   */
  public function getRightOperand(){
    return $this->rightOperand;
  }
}