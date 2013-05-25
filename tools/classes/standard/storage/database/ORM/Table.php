<?php
namespace PPHP\tools\classes\standard\storage\database\ORM;

use \PPHP\tools\patterns\database\query as query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use \PPHP\tools\patterns\interpreter\Metamorphosis;
use \PPHP\tools\patterns\metadata\reflection\ReflectionClass;

/**
 * Данный класс позволяет восстанавливать объекты типа query\Table с использовании отражения персистентного класса.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\storage\database\ORM
 */
class Table extends query\Table implements Metamorphosis{
  /**
   * Имя таблицы, с которой ассоциирован класс.
   */
  const ORM_NAME_TABLE = 'ORM\Table';

  /**
   * Метод восстанавливает SQL компонент Table на основании отражения класса.
   * Класс-основание должен сопровождаться анотацией ORM\Table, хранящей имя таблицы данного класса.
   *
   * @param ReflectionClass $object Исходный объект.
   * @param mixed $driver [optional] Данный аргумент не используется.
   *
   * @throws exceptions\NotFoundDataException Выбрасывается в случае отсутствия требуемых для восстановления данных.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return query\Table Результирующий объект.
   */
  public static function metamorphose($object, $driver = null){
    if(!is_a($object, '\PPHP\tools\patterns\metadata\reflection\ReflectionClass')){
      throw exceptions\InvalidArgumentException::getTypeException('\PPHP\tools\patterns\metadata\reflection\ReflectionClass', get_class($object));
    }

    if(!$object->isMetadataExists(self::ORM_NAME_TABLE)){
      throw new exceptions\NotFoundDataException('Отсутствуют необходимые метаданные [' . self::ORM_NAME_TABLE . '] для формирования объекта.');
    }
    return new query\Table($object->getMetadata(self::ORM_NAME_TABLE));
  }
}