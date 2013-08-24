<?php
namespace PPHP\tests\tools\classes\standard\essence\access\authenticated;

class MockAuthEntity extends \PPHP\tools\classes\standard\essence\access\authenticated\AuthenticatedEntity{
  protected function getSavedState(){
    return get_object_vars($this) + parent::getSavedState();
  }

  protected function setSavedState(array $state){
    parent::setSavedState($state);
    foreach($state as $k => $v){
      if(property_exists($this, $k) && $this::getReflectionProperty($k)->getDeclaringClass()->getName() === get_class()){
        $this->$k = $state[$k];
      }
    }
  }
}

MockAuthEntity::getReflectionClass()->setMetadata('NameTable', 'MockAuthEntity');
MockAuthEntity::getReflectionClass()->setMetadata('KeyTable', 'OID');