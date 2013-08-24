<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Множественное логическое выражение.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class MultiCondition extends Condition{
  /**
   * Левый логический операнд.
   * @var Condition
   */
  private $leftOperand;

  /**
   * Правый логический операнд.
   * @var Condition
   */
  private $rightOperand;

  /**
   * Логический оператор. Одно из следующих значений: AND, OR.
   * @var string
   */
  private $logicOperator;

  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return ['\((' . Condition::getPatterns()['condition'] . ') ' . self::getPatterns()['operator'] . ' (' . Condition::getPatterns()['condition'] . ')\)'];
  }

  /**
   * Метод возвращает массив шаблонов, описывающих различные компоненты шаблонов верификации.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getPatterns($driver = null){
    return ['operator' => '(AND|OR)'];
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
    $mask = parent::reestablish($string);

    return new self(Condition::reestablishCondition($mask[1]), $mask[2], Condition::reestablishCondition($mask[3]));
  }

  /**
   * @param Condition $leftOperand Левый логический операнд.
   * @param string $logicOperator Логический оператор. Одно из следующих значений: AND, OR.
   * @param Condition $rightOperand Правый логический операнд.
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct(Condition $leftOperand, $logicOperator, Condition $rightOperand){
    exceptions\InvalidArgumentException::verifyVal($logicOperator, 's # AND|OR');
    $this->leftOperand = $leftOperand;
    $this->logicOperator = $logicOperator;
    $this->rightOperand = $rightOperand;
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
      return '(' . $this->leftOperand->interpretation($driver) . ' ' . $this->logicOperator . ' ' . $this->rightOperand->interpretation($driver) . ')';
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