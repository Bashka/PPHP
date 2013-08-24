<?php
namespace PPHP\tests\tools\classes\standard\essence\structures\hierarchical;

class MockHierarchicalEntity extends \PPHP\tools\classes\standard\essence\structures\hierarchical\HierarchicalEntity{
  protected $prop;

  protected function getSavedState(){
    return get_object_vars($this);
  }
}

MockHierarchicalEntity::getReflectionClass()->setMetadata('NameTable', 'MockHierarchicalEntity');
MockHierarchicalEntity::getReflectionClass()->setMetadata('KeyTable', 'OID');
MockHierarchicalEntity::getReflectionProperty('prop')->setMetadata('NameFieldTable', 'prop');
MockHierarchicalEntity::getReflectionProperty('hierarchicalChild')->setMetadata('AssocClass', 'PPHP\tests\tools\classes\standard\essence\structures\hierarchical\MockHierarchicalEntity');
MockHierarchicalEntity::getReflectionProperty('hierarchicalChild')->setMetadata('KeyAssocTable', 'OID');