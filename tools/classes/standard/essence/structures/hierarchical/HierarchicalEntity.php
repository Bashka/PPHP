<?php
namespace PPHP\tools\classes\standard\essence\structures\hierarchical;

/**
 * Класс представляет сущности, имеющие неограниченные по вертикали и горизонтали иерархические связи между другими сужностями.
 */
abstract class HierarchicalEntity extends \PPHP\tools\patterns\database\LongObject{
  /**
   * Идентификатор родительской сущности. null если сущность является корнем иерархии.
   * @var HierarchicalEntity|null
   */
  protected $hierarchicalParent;

  /**
   * Множество дочерних объектов.
   * Данное свойтсво должно быть аннотированно для множественной, ассоциативной выборки дочерних элементов с использованием ключей AssocClass и KeyAssocTable.
   * @var \PPHP\tools\patterns\database\associations\LongAssociation
   */
  protected $hierarchicalChild;

  public function getHierarchicalParent(){
    return $this->hierarchicalParent;
  }

  public function setHierarchicalParent($parentOID){
    $this->hierarchicalParent = $parentOID;
  }

  /**
   * @return \PPHP\tools\patterns\database\associations\LongAssociation
   */
  public function &getHierarchicalChild(){
    return $this->hierarchicalChild;
  }
}

HierarchicalEntity::getReflectionClass()->setMetadata('NameTable', 'HierarchicalEntity');
HierarchicalEntity::getReflectionClass()->setMetadata('KeyTable', 'OID');

HierarchicalEntity::getReflectionProperty('hierarchicalParent')->setMetadata('NameFieldTable', 'parent');