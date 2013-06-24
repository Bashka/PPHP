<?php
namespace PPHP\tools\classes\standard\storage\database\ORM;

use PPHP\tools\classes\standard\baseType\exceptions as exceptions;
use PPHP\tools\patterns\database\LongObject;
use PPHP\tools\patterns\database\query as query;
use PPHP\tools\patterns\interpreter\Metamorphosis;
use PPHP\tools\patterns\metadata\reflection\ReflectionClass;

/**
 * Класс восстанавливает SQL инструкцию запроса состояния персистентного объекта на основании его Proxy.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\storage\database\ORM
 */
class Select extends query\Select implements Metamorphosis{
  /**
   * Метод возвращает SQL инструкцию запроса состояний объектов определенного класса без условия отбора.
   * @param ReflectionClass $mainClass Класс-основание.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае отсутствия требуемых для восстановления данных.
   * @return query\Select Результирующая SQL инструкция.
   */
  protected static function getSelectTemplate(ReflectionClass $mainClass){
    $select = new query\Select;
    // Определение основной таблицы
    try{
      $mainTable = Table::metamorphose($mainClass);
    }
    catch(exceptions\NotFoundDataException $e){
      throw $e;
    }
    $select->addTable($mainTable);
    // Формирование списка доступных для редактирования полей
    $mainClassName = $mainClass->getName();
    $fields = $mainClassName::getAllReflectionProperties();
    if(count($fields) == 0){
      throw new exceptions\NotFoundDataException('Объект-основание не имеет ни одного связанного поля в таблице.');
    }
    // Формирование полей запроса
    $classes = [];
    foreach($fields as $fieldName => $fieldReflection){
      // Исключение не аннотированных свойств
      if(!$fieldReflection->isMetadataExists(Field::ORM_FIELD_NAME)){
        continue;
      }
      $declaringClassName = $fieldReflection->getDeclaringClass()->getName();
      $select->addAliasField(new query\FieldAlias(Field::metamorphose($declaringClassName::getReflectionClass(), $fieldName), $fieldName)); // Выброс исключений не предполагается
      if($declaringClassName != $mainClass->getName()){
        // Формирование списка родительских классов
        $classes[] = $declaringClassName;
      }
    }
    // Добавление идентификационного поля
    $OIDField = new query\Field($mainClass->getMetadata(Join::ORM_PK));
    $select->addAliasField(new query\FieldAlias($OIDField->setTable($mainTable), 'OID'));
    // Формирование списка объединений
    $classes = array_unique($classes);
    foreach($classes as $class){
      $select->addJoin(Join::metamorphose($mainClass, $class::getReflectionClass()));
    }

    return $select;
  }

  /**
   * Метод восстанавливает SQL инструкцию Select состояния персистентного объекта на основании его Proxy.
   * В запрос включаются только те свойства, которые анотированы как ORM\ColumnName.
   * Класс объекта должен сопровождаться анотацией ORM\Table, хранящей имя таблицы данного класса.
   * Класс объекта должен сопровождаться анотацией ORM\PK, хранящей имя primary key поля таблицы.
   * @param LongObject $object Исходный объект.
   * @param mixed $driver [optional] Данный аргумент не используется.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае отсутствия требуемых для восстановления данных.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return query\Select Результирующий объект.
   */
  public static function metamorphose($object, $driver = null){
    if(!is_a($object, '\PPHP\tools\patterns\database\LongObject')){
      throw exceptions\InvalidArgumentException::getTypeException('\PPHP\tools\patterns\database\LongObject', get_class($object));
    }
    if(!$object->isOID()){
      throw new exceptions\NotFoundDataException('Исходный объект не идентифицирован.');
    }
    $objectClassReflection = $object::getReflectionClass();
    try{
      $select = self::getSelectTemplate($objectClassReflection);
    }
    catch(exceptions\NotFoundDataException $e){
      throw $e;
    }
    // Формирование условия отбора
    $pkField = Join::getPKField($objectClassReflection);
    $pkField->setTable(Table::metamorphose($objectClassReflection)); // Выброс исключений не предполагается
    $select->insertWhere(new query\Where(new query\LogicOperation($pkField, '=', $object->getOID())));

    return $select;
  }

  /**
   * Метод восстанавливает SQL инструкцию Select состояний множества персистентных объектов на основании их класса и условий отбора.
   * В запрос включаются только те свойства, которые анотированы как ORM\ColumnName.
   * Класс должен сопровождаться анотацией ORM\Table, хранящей имя таблицы данного класса.
   * Класс должен сопровождаться анотацией ORM\PK, хранящей имя primary key поля таблицы.
   * @param ReflectionClass $assocClass Класс основание.
   * @param array $conditions [optional] Массив условий отбора, имеющий следующую структуру: [[имяСвойства, операцияСравнения, значение], ...]. В случае отсутствия данного параметра в результирующем объекте отсутствует условие отбора.
   * @throws exceptions\NotFoundDataException Выбрасывается в случае отсутствия требуемых для восстановления данных.
   * @throws exceptions\InvalidArgumentException Выбрасывается в случае получения параметра неверного типа.
   * @return query\Select Результирующий объект.
   */
  public static function metamorphoseAssociation(ReflectionClass $assocClass, array $conditions = null){
    try{
      $select = self::getSelectTemplate($assocClass);
    }
    catch(exceptions\NotFoundDataException $e){
      throw $e;
    }
    if(!is_null($conditions)){
      $assocClassName = $assocClass->getName();
      $fields = $assocClassName::getAllReflectionProperties();
      $condition = null;
      $andCondition = new query\AndMultiCondition();
      foreach($conditions as $condition){
        if(!array_key_exists($condition[0], $fields)){
          throw new exceptions\NotFoundDataException('Указанного поля отбора [' . $condition[0] . '] нет в классе-основании.');
        }
        try{
          // Предварительная сериализация объектов
          if(($condition[2] instanceof LongObject) && $condition[2]->isOID()){
            $condition[2] = $condition[2]->interpretation(); // Перехват исключений не выполняется в связи с невозможностью их появления
          }
          $condition = new query\LogicOperation(Field::metamorphose($assocClass, $condition[0]), $condition[1], $condition[2]);
        }
        catch(exceptions\InvalidArgumentException $e){
          throw $e;
        }
        catch(exceptions\NotFoundDataException $e){
          throw $e;
        }
        $andCondition->addCondition($condition);
      }
      if(count($conditions) == 1){
        $where = new query\Where($condition);
      }
      else{
        $where = new query\Where($andCondition);
      }
      $select->insertWhere($where);
    }

    return $select;
  }
}