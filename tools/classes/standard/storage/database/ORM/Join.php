<?php
namespace PPHP\tools\classes\standard\storage\database\ORM;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\database\query as query;
use PPHP\tools\patterns\interpreter\Metamorphosis;
use PPHP\tools\patterns\metadata\reflection\ReflectionClass;

/**
 * Данный класс позволяет восстанавливать объекты типа query\Join с использовании персистентного объекта класса LongObject и отражения связываемого класса.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\storage\database\ORM
 */
class Join extends query\Join implements Metamorphosis{
  /**
   * Имя primary key поля, являющегося идентификационным для объектов данного класса.
   */
  const ORM_PK = 'ORM\PK';

  /**
   * Метод восстанавливает SQL инструкцию объединения на основании указанного отражения связываемого класса и их primary key.
   * Класс-основание и связываемый класс должены сопровождаться анотацией ORM\Table, хранящей имя таблицы классов.
   * Класс-основание и связываемый класс должны сопровождаться анотацией ORM\PK, хранящей имя поля primary key, которое ассоциируется с OID персистентных объектов.
   * @param ReflectionClass $object Исходный объект.
   * @param ReflectionClass $driver Отражение связываемого класса.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае отсутствия требуемых для восстановления данных.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return query\Join Результирующий объект.
   */
  public static function metamorphose($object, $driver = null){
    if(!is_a($object, '\PPHP\tools\patterns\metadata\reflection\ReflectionClass')){
      throw exceptions\InvalidArgumentException::getTypeException('\PPHP\tools\patterns\metadata\reflection\ReflectionClass', get_class($object));
    }
    if(!is_a($driver, '\PPHP\tools\patterns\metadata\reflection\ReflectionClass')){
      throw exceptions\InvalidArgumentException::getTypeException('\PPHP\tools\patterns\metadata\reflection\ReflectionClass', get_class($driver));
    }
    // Определения связываемой таблицы
    try{
      $tableObject = Table::metamorphose($object);
      $tableClass = Table::metamorphose($driver);
    }
    catch(exceptions\NotFoundDataException $e){
      throw $e;
    }
    // Формирование условия
    if(!$object->isMetadataExists(self::ORM_PK)){
      throw new exceptions\NotFoundDataException('Отсутствуют необходимые метаданные [' . self::ORM_PK . '] для формирования объекта.');
    }
    if(!$driver->isMetadataExists(self::ORM_PK)){
      throw new exceptions\NotFoundDataException('Отсутствуют необходимые метаданные [' . self::ORM_PK . '] для формирования объекта.');
    }
    $fieldObject = new query\Field($object->getMetadata(self::ORM_PK));
    $fieldObject->setTable($tableObject);
    $fieldClass = new query\Field($driver->getMetadata(self::ORM_PK));
    $fieldClass->setTable($tableClass);

    return new query\Join(query\Join::INNER, $tableClass, new query\LogicOperation($fieldObject, '=', $fieldClass));
  }

  /**
   * Метод возвращает SQL компонент Field для primary key класса-основания.
   * @param ReflectionClass $class Класс-основание.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае отсутствия требуемых для восстановления данных.
   * @return query\Field Результирующий объект.
   */
  public static function getPKField(ReflectionClass $class){
    if(!$class->isMetadataExists(self::ORM_PK)){
      throw new exceptions\NotFoundDataException('Отсутствуют необходимые метаданные [' . self::ORM_PK . '] для формирования объекта.');
    }

    return new query\Field($class->getMetadata(self::ORM_PK));
  }
}