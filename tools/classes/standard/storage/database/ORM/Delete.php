<?php
namespace PPHP\tools\classes\standard\storage\database\ORM;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\database\persistent\LongObject;
use PPHP\tools\patterns\database\query as query;
use PPHP\tools\patterns\interpreter\Metamorphosis;

/**
 * Класс восстанавливает SQL инструкцию удаления состояния персистентного объекта на основании его Proxy или другого условия отбора.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\storage\database\ORM
 */
class Delete implements Metamorphosis{
  /**
   * Метод восстанавливает SQL инструкцию Delete состояния персистентного объекта на основании его Proxy.
   * Метод учитывает иерархию наследования таблиц и возвращает транзакцию запросов.
   * В запрос включаются только те свойства, которые анотированы как ORM\ColumnName.
   * Класс объекта должен сопровождаться анотацией ORM\Table, хранящей имя таблицы данного класса.
   * Класс объекта должен сопровождаться анотацией ORM\PK, хранящей имя primary key поля таблицы.
   * @param LongObject $object Исходный объект.
   * @param query\Where $driver [optional] Если данный параметр передан, он используется как условие отбора в инструкции.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае отсутствия требуемых для восстановления данных.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return query\Delete[] Транзакция запросов.
   */
  public static function metamorphose($object, $driver = null){
    if(!is_a($object, '\PPHP\tools\patterns\database\persistent\LongObject')){
      throw exceptions\InvalidArgumentException::getTypeException('\PPHP\tools\patterns\database\persistent\LongObject', get_class($object));
    }
    if(!$object->isOID()){
      throw new exceptions\NotFoundDataException('Исходный объект не идентифицирован.');
    }
    $deletes = [];
    $reflectionClass = $object->getReflectionClass();
    do{
      $tableName = $reflectionClass->getMetadata(Table::ORM_NAME_TABLE);
      if(!is_null($tableName) && !empty($tableName)){
        $delete = new query\Delete(new query\Table($tableName));
        if(!is_null($driver) && is_a($driver, '\PPHP\tools\patterns\database\query\Where')){
          $deletes[] = $delete->insertWhere($driver);
        }
        else{
          $deletes[] = $delete->insertWhere(new query\Where(new query\LogicOperation(Join::getPKField($reflectionClass), '=', $object->getOID())));
        }
      }
      $parentReflectionClassName = $reflectionClass->getName();
    } while(($parentReflectionClassName != 'PPHP\tools\patterns\interpreter\RestorableAdapter') && ($reflectionClass = $parentReflectionClassName::getParentReflectionClass()));

    return $deletes;
  }
}