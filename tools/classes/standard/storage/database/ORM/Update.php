<?php
namespace PPHP\tools\classes\standard\storage\database\ORM;

use \PPHP\tools\patterns\database\LongObject;
use \PPHP\tools\patterns\database\query as query;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use \PPHP\tools\patterns\interpreter\Metamorphosis;

/**
 * Класс восстанавливает SQL инструкцию обновления состояния персистентного объекта.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\storage\database\ORM
 */
class Update implements Metamorphosis{
  /**
   * Метод восстанавливает SQL инструкцию Update состояния персистентного объекта.
   * Метод учитывает иерархию наследования таблиц и возвращает транзакцию запросов.
   * В запрос включаются только те свойства, которые анотированы как ORM\ColumnName и которые возвращаются методом TOriginator::getSavedState исходного объекта.
   * Класс объекта должен сопровождаться анотацией ORM\Table, хранящей имя таблицы данного класса.
   * Класс объекта должен сопровождаться анотацией ORM\PK, хранящей имя primary key поля таблицы.
   *
   * @param LongObject $object Исходный объект.
   * @param query\Where $driver [optional] Если данный параметр передан, он используется как условие отбора в инструкции.
   *
   * @throws exceptions\NotFoundDataException Выбрасывается в случае отсутствия требуемых для восстановления данных.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return query\Update[] Транзакция запросов.
   */
  public static function metamorphose($object, $driver = null){
    if(!is_a($object, '\PPHP\tools\patterns\database\LongObject')){
      throw exceptions\InvalidArgumentException::getTypeException('\PPHP\tools\patterns\database\LongObject', get_class($object));
    }
    if(!$object->isOID()){
      throw new exceptions\NotFoundDataException('Исходный объект не идентифицирован.');
    }

    $updates = [];
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
        if(!isset($updates[$tableName])){
          $updates[$tableName] = new query\Update($table);
          $declaringClassName = $reflectionProperty->getDeclaringClass()->getName();
          if(!is_null($driver) && is_a($driver, '\PPHP\tools\patterns\database\query\Where')){
            $updates[$tableName] = $updates[$tableName]->insertWhere($driver);
          }
          else{
            try{
              $updates[$tableName]->insertWhere(new query\Where(new query\LogicOperation(Join::getPKField($declaringClassName::getReflectionClass()), '=', $object->getOID())));
            }
            catch(exceptions\NotFoundDataException $e){
              throw $e;
            }
          }
        }
        $updates[$tableName]->addData($field, $v);
      }
    }
    // Индексация результата целыми числами
    $result = [];
    foreach($updates as $update){
      $result[] = $update;
    }
    return $result;
  }
}