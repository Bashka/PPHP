<?php
namespace PPHP\tools\classes\standard\storage\database\ORM;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\database\persistent\LongObject;
use PPHP\tools\patterns\database\query as query;
use PPHP\tools\patterns\interpreter\Metamorphosis;

/**
 * Класс восстанавливает SQL инструкцию добавления состояния персистентного объекта.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\storage\database\ORM
 */
class Insert implements Metamorphosis{
  /**
   * Метод восстанавливает SQL инструкцию Insert состояния персистентного объекта.
   * Метод учитывает иерархию наследования таблиц и возвращает транзакцию запросов.
   * В запрос включаются только те свойства, которые анотированы как ORM\ColumnName и которые возвращаются методом TOriginator::getSavedState исходного объекта.
   * Класс объекта должен сопровождаться анотацией ORM\Table, хранящей имя таблицы данного класса.
   * Класс объекта должен сопровождаться анотацией ORM\PK, хранящей имя primary key поля таблицы.
   * @param LongObject $object Исходный объект.
   * @param integer $driver Идентификатор новой записи.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае отсутствия требуемых для восстановления данных.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return query\Insert[] Транзакция запросов.
   */
  public static function metamorphose($object, $driver = null){
    if(!is_a($object, '\PPHP\tools\patterns\database\persistent\LongObject')){
      throw exceptions\InvalidArgumentException::getTypeException('\PPHP\tools\patterns\database\persistent\LongObject', get_class($object));
    }
    exceptions\InvalidArgumentException::verifyType($driver, 'i');
    if($object->isOID()){
      throw new exceptions\NotFoundDataException('Исходный объект идентифицирован.');
    }
    $inserts = [];
    $state = $object->createMemento()->getState($object); // Выброс исключений не предполагается
    foreach($state as $k => &$v){
      // Предварительная сериализация объектов в полученом массиве
      if(($v instanceof LongObject) && $v->isOID()){
        $v = $v->interpretation(); // Перехват исключений не выполняется в связи с невозможностью их появления
      }
      // Замена null на пустую строку
      if(is_null($v)){
        $v = '';
      }
      $reflectionProperty = $object->getReflectionProperty($k); // Выброс исключений не предполагается
      if($reflectionProperty->isMetadataExists(Field::ORM_FIELD_NAME)){
        $field = Field::metamorphose($object->getReflectionClass(), $k); // Выброс исключений не предполагается
        $table = $field->getTable();
        $tableName = $table->getTableName();
        if(!isset($inserts[$tableName])){
          $inserts[$tableName] = new query\Insert($table);
          $declaringClassName = $reflectionProperty->getDeclaringClass()->getName();
          try{
            $inserts[$tableName]->addData(Join::getPKField($declaringClassName::getReflectionClass()), $driver);
          }
          catch(exceptions\NotFoundDataException $e){
            throw $e;
          }
        }
        $inserts[$tableName]->addData($field, $v);
      }
    }
    // Индексация результата целыми числами
    $result = [];
    foreach($inserts as $insert){
      $result[] = $insert;
    }

    return $result;
  }
}