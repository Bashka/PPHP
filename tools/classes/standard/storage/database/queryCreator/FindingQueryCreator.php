<?php
namespace PPHP\tools\classes\standard\storage\database\queryCreator;

/**
 * Класс позволяет формировать поисковые запросы SELECT на основании требуемых значений полей.
 */
class FindingQueryCreator extends IndividualQueryCreator{
  /**
   * Метод возвращает поисковой запрос SELECT, значение полей которого соответствуют требуемым во втором аргументе.
   * Поисковые поля автоматически преобразовываются согласно метаданным искомого класса.
   * В запрос не попадут поля родительских таблиц, объявленных в классах как private.
   * В запрос попадут только те поля, для которых заданы метаданные NameFieldTable.
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionClass $reflectionClass Отражение класса - основания.
   * @param array $requiredProperties Требуемые значения полей.
   * @return \PPHP\tools\patterns\database\query\Select
   */
  public function createFindingQuery(\PPHP\tools\patterns\metadata\reflection\ReflectionClass $reflectionClass, array $requiredProperties){
    $mainClass = $reflectionClass->getName();
    $metadataManager = new \PPHP\tools\patterns\metadata\MetadataManager($mainClass);
    $allProperties = $metadataManager->getAllReflectionPropertiesWithMetadata('NameFieldTable');
    $select = new \PPHP\tools\patterns\database\query\Select();
    $mainTable = new \PPHP\tools\patterns\database\query\Table($reflectionClass->getMetadata('NameTable'));
    $select->addTable($mainTable);
    $parentTables = [];
    $keyFieldName = $reflectionClass->getMetadata('KeyTable');
    $keyField = new \PPHP\tools\patterns\database\query\Field($keyFieldName);
    $keyField->setTable($mainTable);
    $multiCondition = new \PPHP\tools\patterns\database\query\AndMultiCondition();
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
      if(isset($requiredProperties[$property->getName()])){
        $multiCondition->addCondition(new \PPHP\tools\patterns\database\query\LogicOperation($field, '=', $this->serializeIndividualObject($requiredProperties[$property->getName()])));
      }
    }
    // Добавление в выборку идентификатора
    $select->addAliasField(new \PPHP\tools\patterns\database\query\FieldAlias($keyField, 'OID'));
    // Добавление условия выборки по идентификатору
    if(isset($requiredProperties['OID'])){
      $multiCondition->addCondition(new \PPHP\tools\patterns\database\query\LogicOperation($keyField, '=', $requiredProperties['OID']));
    }
    $select->insertWhere(new \PPHP\tools\patterns\database\query\Where($multiCondition));
    return $select;
  }
}
