<?php
namespace PPHP\tools\classes\standard\storage\database\queryCreator;

/**
 * Класс служит для формирования SQL запросов к реляционным базам данных на основании объекта.
 */
class IndividualQueryCreator extends QueryCreator{
  /**
   * Сервис генерации уникальных идентификаторов.
   * @var \PPHP\services\database\identification\Autoincrement
   */
  protected $autoincrement;

  function __construct(\PPHP\services\database\identification\Autoincrement $autoinc){
    $this->autoincrement = $autoinc;
  }


  /**
   * Метод пытается сериализовать объект в единичную ссылку или во внедренное значение.
   * @static
   * @param mixed $object Сериализуемые данные.
   * @return string|mixed Возвращает сериализованный объект или первоначальные данные, если их невозможно сериализовать.
   */
  static protected function serializeIndividualObject($object){
    if(is_object($object)){
      if(($object instanceof \PPHP\tools\patterns\database\LongObject) && $object->isOID()){
        $object = $object->getLinkOID();
      }
      elseif($object instanceof \Serializable){
        $object = serialize($object);
      }
    }
    return $object;
  }

  /**
   * Метод возвращает массив значений свойств объекта.
   * @param \PPHP\tools\patterns\memento\Originator $object Объект - источник.
   * @return array Ассоциативный массив значений свойств объекта.
   */
  protected function getPropertiesValues(\PPHP\tools\patterns\memento\Originator $object){
    $prop = $object->createMemento()->getState($object);
    $prop = array_map([$this, 'serializeIndividualObject'], $prop);
    return $prop;
  }

  /**
   * Метод формирует SQL запрос записи об объекте из иерархии таблиц.
   * В запрос не попадут поля родительских таблиц, объявленных в классах как private.
   * В запрос попадут только те поля, для которых заданы метаданные NameFieldTable.
   * Поле содержащее OID во всей иерархии таблиц БД должно иметь одинаковое имя поля.
   * @abstract
   * @param \PPHP\tools\patterns\database\LongObject $object Объект - основание.
   * @throws \PPHP\tools\patterns\database\identification\IncorrectOIDException Выбрасывается в случае, если объект не идентифицирован.
   * @return \PPHP\tools\patterns\database\query\Select
   */
  public function createSelectQuery(\PPHP\tools\patterns\database\LongObject $object){
    if(!$object->isOID()){
      throw new \PPHP\tools\patterns\database\identification\IncorrectOIDException;
    }

    $mainClass = get_class($object);
    $reflectionMainClass = $object->getReflectionClass();
    $metadataManager = new \PPHP\tools\patterns\metadata\MetadataManager($mainClass);
    $allProperties = $metadataManager->getAllReflectionPropertiesWithMetadata('NameFieldTable');
    $select = new \PPHP\tools\patterns\database\query\Select();
    $mainTable = new \PPHP\tools\patterns\database\query\Table($reflectionMainClass->getMetadata('NameTable'));
    $select->addTable($mainTable);
    $parentTables = [];
    $keyFieldName = $reflectionMainClass->getMetadata('KeyTable');
    $keyField = new \PPHP\tools\patterns\database\query\Field($keyFieldName);
    $keyField->setTable($mainTable);
    foreach($allProperties as $property){
      $field = new \PPHP\tools\patterns\database\query\Field($property->getMetadata('NameFieldTable'));
      $aliasField = new \PPHP\tools\patterns\database\query\FieldAlias($field, $property->getName());
      if($property->class != $mainClass){
        if(!isset($parentTables[$property->class])){
          $parentClass = $property->class;
          $parentClass = $parentClass::getReflectionClass();
          $parentTables[$property->class] = new \PPHP\tools\patterns\database\query\Table($parentClass->getMetadata('NameTable'));
          $parentKeyField = new \PPHP\tools\patterns\database\query\Field($parentClass->getMetadata('KeyTable'));
          $parentKeyField->setTable($parentTables[$property->class]);
          $join = new \PPHP\tools\patterns\database\query\Join('INNER', $parentTables[$property->class], new \PPHP\tools\patterns\database\query\LogicOperation($keyField, '=', $parentKeyField));
          $select->addJoin($join);
        }
      }
      $select->addAliasField($aliasField);
    }
    $select->insertWhere(new \PPHP\tools\patterns\database\query\Where(new \PPHP\tools\patterns\database\query\LogicOperation($keyField, '=', $object->getOID())));
    return $select;
  }

  /**
   * Метод формирует SQL запрос для обновления записей об объекте в иерархии таблиц.
   * В запрос не попадут поля родительских таблиц, объявленных в классах как private.
   * В запрос попадут только те поля, для которых заданы метаданные NameFieldTable.
   * @abstract
   * @param \PPHP\tools\patterns\database\LongObject $object Объект - основание.
   * @throws \PPHP\tools\patterns\database\identification\IncorrectOIDException Выбрасывается в случае, если объект не идентифицирован.
   * @return \PPHP\tools\patterns\database\query\Update[] Танзакция изменения иерархии
   */
  public function createUpdateQuery(\PPHP\tools\patterns\database\LongObject $object){
    if(!$object->isOID()){
      throw new \PPHP\tools\patterns\database\identification\IncorrectOIDException;
    }

    $propertiesValues = $this->getPropertiesValues($object);
    $metadataManager = new \PPHP\tools\patterns\metadata\MetadataManager(get_class($object));
    $allProperties = $metadataManager->getAllReflectionPropertiesWithMetadata('NameFieldTable');
    $updates = [];
    foreach($allProperties as $property){
      $class = $property->class;
      if(!isset($updates[$class])){
        $reflectionClass = $class::getReflectionClass();
        $updates[$property->class] = new \PPHP\tools\patterns\database\query\Update(new \PPHP\tools\patterns\database\query\Table($reflectionClass->getMetadata('NameTable')));

        $where = new \PPHP\tools\patterns\database\query\Where(new \PPHP\tools\patterns\database\query\LogicOperation(new \PPHP\tools\patterns\database\query\Field($reflectionClass->getMetadata('KeyTable')), '=', $object->getOID()));
        $updates[$property->class]->insertWhere($where);
      }
      $field = new \PPHP\tools\patterns\database\query\Field($property->getMetadata('NameFieldTable'));
      $updates[$property->class]->addData($field, $propertiesValues[$property->getName()]);
    }
    return $updates;
  }

  /**
   * Метод формирует SQL запрос для удаления записей об объекте в иерархии таблиц.
   * @abstract
   * @param \PPHP\tools\patterns\database\LongObject $object Объект - основание.
   * @throws \PPHP\tools\patterns\database\identification\IncorrectOIDException Выбрасывается в случае, если объект не идентифицирован.
   * @return \PPHP\tools\patterns\database\query\Delete[]
   */
  public function createDeleteQuery(\PPHP\tools\patterns\database\LongObject $object){
    if(!$object->isOID()){
      throw new \PPHP\tools\patterns\database\identification\IncorrectOIDException;
    }

    $deletes = [];
    $reflectionClass = $object->getReflectionClass();
    do{
      $tableName = $reflectionClass->getMetadata('NameTable');
      if(!is_null($tableName)){
        $deletes[$tableName] = new \PPHP\tools\patterns\database\query\Delete(new \PPHP\tools\patterns\database\query\Table($tableName));
        $keyField = new \PPHP\tools\patterns\database\query\Field($reflectionClass->getMetadata('KeyTable'));
        $deletes[$tableName]->insertWhere(new \PPHP\tools\patterns\database\query\Where(new \PPHP\tools\patterns\database\query\LogicOperation($keyField, '=', $object->getOID())));
      }
      $parentReflectionClassName = $reflectionClass->getName();
    }
    while($reflectionClass = $parentReflectionClassName::getParentReflectionClass());
    return $deletes;
  }

  /**
   * Метод формирует SQL запрос для добавления записей об объекте в иерархии таблиц.
   * В запрос не попадут поля родительских таблиц, объявленных в классах как private.
   * В запрос попадут только те поля, для которых заданы метаданные NameFieldTable.
   * Родительские таблицы будут использовать тот же идентификатор записи, что и конкретная таблица записываемого объекта.
   * После выполнения метода, целевой объект получает новый идентификатор.
   * @abstract
   * @param \PPHP\tools\patterns\database\LongObject $object Объект - основание (proxy).
   * @throws \PPHP\tools\patterns\database\identification\IncorrectOIDException Выбрасывается в случае, если на момент генерации запроса объект имел идентификатор.
   * @return \PPHP\tools\patterns\database\query\Insert[]
   */
  public function createInsertQuery(\PPHP\tools\patterns\database\LongObject &$object){
    if($object->isOID()){
      throw new \PPHP\tools\patterns\database\identification\IncorrectOIDException;
    }
    $reflectionProperties = $this->getPropertiesValues($object);
    $mainClass = get_class($object);
    $metadataManager = new \PPHP\tools\patterns\metadata\MetadataManager($mainClass);
    $allProperties = $metadataManager->getAllReflectionPropertiesWithMetadata('NameFieldTable');
    $inserts = [];
    foreach($allProperties as $property){
      $class = $property->class;
      if(!isset($inserts[$class])){
        $reflectionClass = $class::getReflectionClass();
        $inserts[$property->class] = new \PPHP\tools\patterns\database\query\Insert(new \PPHP\tools\patterns\database\query\Table($reflectionClass->getMetadata('NameTable')));
      }
      $field = new \PPHP\tools\patterns\database\query\Field($property->getMetadata('NameFieldTable'));
      $inserts[$property->class]->addData($field, $reflectionProperties[$property->getName()]);
    }
    $newOID = $this->autoincrement->generateOID();
    $object->setOID($newOID);
    foreach($inserts as $insert){
      $field = new \PPHP\tools\patterns\database\query\Field($metadataManager->getReflectionClass()->getMetadata('KeyTable'));
      $insert->addData($field, $newOID);
    }
    return $inserts;
  }
}
