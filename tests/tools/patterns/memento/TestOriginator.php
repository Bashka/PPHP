<?php
namespace PPHP\tests\tools\patterns\memento;

use \PPHP\tools\patterns\memento as memento;

class TestOriginator implements memento\Originator{
  use memento\TOriginator;

  private $testVar = 5;

  public function getTestVar(){
    return $this->testVar;
  }

  public function setTestVar($testVar){
    $this->testVar = $testVar;
  }

  protected function getSavedState(){
    return ['testVar' => $this->testVar];
  }
}
