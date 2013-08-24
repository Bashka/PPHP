<?php
namespace PPHP\tests\tools\patterns\database;

class TestAssoc extends \PPHP\tools\patterns\database\LongObject{
  private $propOne;

  private $propTwo;

  protected function getSavedState(){
    return get_object_vars($this);
  }

  protected function setSavedState(array $state){
    foreach($state as $k => $v){
      $this->$k = $v;
    }
  }

  public function getPropOne(){
    return $this->propOne;
  }

  public function getPropTwo(){
    return $this->propTwo;
  }

  /**
   * @param  $propOne
   */
  public function setPropOne($propOne){
    $this->propOne = $propOne;
  }

  /**
   * @param  $propTwo
   */
  public function setPropTwo($propTwo){
    $this->propTwo = $propTwo;
  }
}

TestAssoc::getReflectionProperty('propOne')->setMetadata('NameFieldTable', 'propOneField');
TestAssoc::getReflectionProperty('propTwo')->setMetadata('NameFieldTable', 'propTwoField');
TestAssoc::getReflectionClass()->setMetadata('NameTable', 'Assoc');
TestAssoc::getReflectionClass()->setMetadata('KeyTable', 'Key');
