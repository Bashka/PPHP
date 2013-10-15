<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions\StructureException;

/**
 * Дочерние классы данного класса являются представлением логической конструкции.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
abstract class Condition extends ComponentQuery{
  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getPatterns($driver = null){
    return ['condition' => ' ?\(.+\) ?'];
  }

  /**
   * Метод выполняет восстановление дочерних классов данного класса из строки.
   * Метод автоматически определяет класс восстанавливаемого объекта на основании анализа структуры исходной строки.
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\StructureException Выбрасывается в случае, если исходная строка не отвечает требования структуры.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return \PPHP\tools\patterns\database\query\Condition Результирующий объект.
   */
  public static function reestablishCondition($string, $driver = null){
    if(LogicOperation::isReestablish($string)){
      return LogicOperation::reestablish($string);
    }
    elseif(MultiCondition::isReestablish($string)){
      return MultiCondition::reestablish($string);
    }
    elseif(INLogicOperation::isReestablish($string)){
      return INLogicOperation::reestablish($string);
    }
    elseif(AndMultiCondition::isReestablish($string)){
      return AndMultiCondition::reestablish($string);
    }
    elseif(OrMultiCondition::isReestablish($string)){
      return OrMultiCondition::reestablish($string);
    }
    else{
      throw new StructureException('Недопустимая структура исходной строки. Невозможно определить условие.');
    }
  }
}
