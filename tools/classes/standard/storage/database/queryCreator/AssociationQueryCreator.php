<?php
namespace PPHP\tools\classes\standard\storage\database\queryCreator;

/**
 * Данный класс позволяет конструировать запросы для восстановления множественных ассоциаций.
 */
class AssociationQueryCreator extends FindingQueryCreator{
  /**
   * Метод создает запрос на выборку ассоциациированных с данной сущностью сущностей.
   * Ассоциируемым классом считается класс, определенный в метаданных AssocClass.
   * Ключ ассоциации определяется в метаданных KeyAssocTable.
   * @param \PPHP\tools\patterns\database\LongObject $object Объект - основание.
   * @param \PPHP\tools\patterns\metadata\reflection\ReflectionProperty $reflectAssocProp Отображение свойства, являющееся ассоциативным.
   * @return \PPHP\tools\patterns\database\query\Select
   */
  public function createAssociationSelectQuery(\PPHP\tools\patterns\database\LongObject $object, \PPHP\tools\patterns\metadata\reflection\ReflectionProperty $reflectAssocProp){
    $metadataManagerAssocClass = new \PPHP\tools\patterns\metadata\MetadataManager($reflectAssocProp->getMetadata('AssocClass'));
    $reflectionPropertiesAssocClass = $metadataManagerAssocClass->getAllReflectionPropertiesWithMetadata('NameFieldTable');
    $select = new \PPHP\tools\patterns\database\query\Select();
    foreach($reflectionPropertiesAssocClass as $property){
      $field = new \PPHP\tools\patterns\database\query\FieldAlias(new \PPHP\tools\patterns\database\query\Field($property->getMetadata('NameFieldTable')), $property->getName());
      $select->addAliasField($field);
    }
    $field = new \PPHP\tools\patterns\database\query\FieldAlias(new \PPHP\tools\patterns\database\query\Field($metadataManagerAssocClass->getMetadataClass('KeyTable')), 'OID');
    $select->addAliasField($field);
    $select->addTable(new \PPHP\tools\patterns\database\query\Table($metadataManagerAssocClass->getMetadataClass('NameTable')));
    $select->insertWhere(new \PPHP\tools\patterns\database\query\Where(new \PPHP\tools\patterns\database\query\LogicOperation(new \PPHP\tools\patterns\database\query\Field($reflectAssocProp->getMetadata('KeyAssocTable')), '=', self::serializeIndividualObject($object))));
    return $select;
  }
}