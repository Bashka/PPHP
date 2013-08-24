<?php
namespace PPHP\tools\patterns\database\query;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;

/**
 * Класс позволяет добавить псевдоним полю.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\database\query
 */
class FieldAlias extends Alias{
  protected static function reestablishChild($string, $driver = null){
    try{
      return Field::reestablish($string);
    }
    catch(exceptions\StructureException $e){
      throw $e;
    }
    catch(exceptions\InvalidArgumentException $e){
      throw $e;
    }
  }

  /**
   * Метод возвращает массив шаблонов, любому из которых должна соответствовать строка, из которой можно интерпретировать объект вызываемого класса.
   * @param mixed $driver [optional] Данные, позволяющие изменить логику интерпретации исходной строки.
   * @return string[]
   */
  public static function getMasks($driver = null){
    return ['(?:(?:' . Field::getMasks()[0] . ')|(?:' . Field::getMasks()[1] . ')) as ' . Alias::getPatterns()['aliasValue']];
  }

  /**
   * @param Field $component Поле, к которому добавляется псевдоним. Ожидается объект класса Field.
   * @param string $alias Псевдоним поля.
   * @throws exceptions\InvalidArgumentException Выбрасывается при передаче параметра неверного типа.
   */
  function __construct($component, $alias){
    // Логическая проверка осуществляется в связи с поддержкой семантики родительского класса
    if(!($component instanceof Field)){
      throw exceptions\InvalidArgumentException::getTypeException('Field', gettype($component));
    }
    parent::__construct($component, $alias);
  }
}
