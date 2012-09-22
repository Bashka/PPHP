<?php
namespace PPHP\tests\tools\classes\standard\essence\access\authenticated;

class MockAuthEntity extends \PPHP\tools\classes\standard\essence\access\authenticated\AuthenticatedEntity{
  protected function getSavedState(){
    return get_object_vars($this);
  }
}
MockAuthEntity::getReflectionClass()->setMetadata('NameTable', 'MockAuthEntity');
MockAuthEntity::getReflectionClass()->setMetadata('KeyTable', 'OID');