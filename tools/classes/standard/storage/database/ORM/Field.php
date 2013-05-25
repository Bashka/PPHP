<?php
namespace PPHP\tools\classes\standard\storage\database\ORM;

use \PPHP\tools\patterns\database\query as query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use \PPHP\tools\patterns\interpreter\Metamorphosis;
use \PPHP\tools\patterns\metadata\reflection\ReflectionClass;

/**
 * Данный класс позволяет восстанавливать объекты типа query\Field с использовании персистентного объекта класса LongObject и имени его свойства.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\storage\database\ORM
 */
class Field extends query\Field implements Metamorphosis{
  /**
   * Имя поля таблицы, с которым ассоциированно свойство.
   */
  const ORM_FIELD_NAME = 'ORM\ColumnName';

  /**
   * Метод восстанавливает SQL компонент Field на основании требуемого свойства персистентного класса.
   * Свойство класса-основания должно сопровождаться анотацией ORM\ColumnName, хранящей имя ассоциированного поля в таблице данного класса.
   * Класс-основание должен сопровождаться анотацией ORM\Table, хранящей имя таблицы данного класса.
   *
   * @param ReflectionClass $object Отражение класса-оснвования.
   * @param string $driver Имя свойства объекта, с которым ассоциировано поле.
   *
   * @throws exceptions\NotFoundDataException Выбрасывается в случае отсутствия требуемых для восстановления данных.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return query\Field Результирующий объект.
   */
  public static function metamorphose($object, $driver = null){
    if(!is_a($object, '\PPHP\tools\patterns\metadata\reflection\ReflectionClass')){
      throw exceptions\InvalidArgumentException::getTypeException('\PPHP\tools\patterns\metadata\reflection\ReflectionClass', get_class($object));
    }
    exceptions\InvalidArgumentException::verifyType($driver, 'S');

    // Формирование поля
    $className = $object->getName();
    try{
      $reflectionField = $className::getReflectionProperty($driver);
    }
    catch(exceptions\ComponentClassException $e){
      throw new exceptions\NotFoundDataException('Отсутствует требуемое свойство [' . $driver . '] у класса [' . $className->getName() . '].', 1, $e);
    }

    if(!$reflectionField->isMetadataExists(self::ORM_FIELD_NAME)){
      throw new exceptions\NotFoundDataException('Отсутствуют необходимые метаданные [' . self::ORM_FIELD_NAME . '] свойства ['.$driver.'] для формирования объекта.');
    }
    $field = new query\Field($reflectionField->getMetadata(self::ORM_FIELD_NAME));

    // Поиск класса, к которому относится поле
    $reflectionClass = $reflectionField->getDeclaringClass()->getName();
    try{
      $field->setTable(Table::metamorphose($reflectionClass::getReflectionClass()));
    }
    catch(exceptions\NotFoundDataException $e){
      throw $e;
    }

    return $field;
  }
}