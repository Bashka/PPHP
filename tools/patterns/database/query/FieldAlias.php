<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;
use PPHP\tools\classes\standard\baseType\exceptions\StructureException;

/**
 * Класс позволяет добавить псевдоним полю.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class FieldAlias extends Alias{
  /**
   * Метод выполняет восстановление дочерних классов данного класса из строки.
   * Метод автоматически определяет класс восстанавливаемого объекта на основании анализа структуры исходной строки.
   * @param string $string Исходная строка.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\StructureException Выбрасывается в случае, если исходная строка не отвечает требования структуры.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return Field Результирующий объект.
   */
  protected static function reestablishChild($string, $driver = null){
    try{
      return Field::reestablish($string);
    }
    catch(StructureException $e){
      throw $e;
    }
    catch(InvalidArgumentException $e){
      throw $e;
    }
  }

  /**
   * @prototype \PPHP\tools\patterns\interpreter\TRestorable
   */
  public static function getMasks($driver = null){
    return ['(?:(?:' . Field::getMasks()[0] . ')|(?:' . Field::getMasks()[1] . ')) as ' . Alias::getPatterns()['aliasValue']];
  }

  /**
   * @param \PPHP\tools\patterns\database\query\Field $component Поле, к которому добавляется псевдоним. Ожидается объект класса Field.
   * @param string $alias Псевдоним поля.
   * @throws \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct(ComponentQuery $component, $alias){
    // Логическая проверка осуществляется в связи с поддержкой семантики родительского класса
    if(!($component instanceof Field)){
      throw InvalidArgumentException::getTypeException('Field', gettype($component));
    }
    parent::__construct($component, $alias);
  }
}
