<?php
namespace PPHP\tests\tools\classes\standard\storage\database;

class MockEssenceManager extends \PPHP\tools\classes\standard\storage\database\EssenceManager{
  public function addEssence(\PPHP\tools\patterns\database\LongObject $essence){
    return parent::addEssence($essence);
  }

  public function removeEssence(\PPHP\tools\patterns\database\LongObject $essence){
    return parent::removeEssence($essence);
  }

  public function removeAssoc(\PPHP\tools\patterns\database\associations\LongAssociation $assoc){
    return parent::removeAssoc($assoc);
  }

  public function compositeRemoval(\PPHP\tools\patterns\database\associations\LongAssociation $assoc, array $gettersEssence){
    return parent::compositeRemoval($assoc, $gettersEssence);
  }

  public function findingRemoveAssoc(\PPHP\tools\patterns\metadata\reflection\ReflectionClass $assocClass, \PPHP\tools\patterns\metadata\reflection\ReflectionProperty $key, \PPHP\tools\patterns\database\LongObject $essence){
    return parent::findingRemoveAssoc($assocClass, $key, $essence);
  }

  public function findingCompositeRemoval(\PPHP\tools\patterns\metadata\reflection\ReflectionClass $assocClass, \PPHP\tools\patterns\metadata\reflection\ReflectionProperty $key, \PPHP\tools\patterns\database\LongObject $essence, array $gettersEssence){
    return parent::findingCompositeRemoval($assocClass, $key, $essence, $gettersEssence);
  }

  public function updateEssence(\PPHP\tools\patterns\database\LongObject $essence){
    return parent::updateEssence($essence);
  }

  public function recoverEssence(\PPHP\tools\patterns\database\LongObject $essence){
    return parent::recoverEssence($essence);
  }

  public function findEssence(\PPHP\tools\patterns\database\LongObject &$essence, array $requiredProperties){
    parent::findEssence($essence, $requiredProperties);
  }

  public function findEssences(\PPHP\tools\patterns\metadata\reflection\ReflectionClass $essenceClass, array $requiredProperties){
    return parent::findEssences($essenceClass, $requiredProperties);
  }

  public function recoverComponents(\PPHP\tools\patterns\database\associations\LongAssociation $assoc, \PPHP\tools\patterns\metadata\reflection\ReflectionMethod $getterEssence){
    return parent::recoverComponents($assoc, $getterEssence);
  }

  public function recoverFindAssoc(\PPHP\tools\patterns\metadata\reflection\ReflectionClass $assocClass, \PPHP\tools\patterns\metadata\reflection\ReflectionProperty $key, \PPHP\tools\patterns\database\LongObject $essence){
    return parent::recoverFindAssoc($assocClass, $key, $essence);
  }

  public function recoverFindComponents(\PPHP\tools\patterns\metadata\reflection\ReflectionClass $assocClass, \PPHP\tools\patterns\metadata\reflection\ReflectionProperty $key, \PPHP\tools\patterns\database\LongObject $essence, \PPHP\tools\patterns\metadata\reflection\ReflectionMethod $getterEssence){
    return parent::recoverFindComponents($assocClass, $key, $essence, $getterEssence);
  }

}
