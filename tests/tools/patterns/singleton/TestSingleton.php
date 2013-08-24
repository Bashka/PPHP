<?php
namespace PPHP\tests\tools\patterns\singleton;

use \PPHP\tools\patterns\singleton as singleton;

class TestSingleton implements singleton\Singleton{
  use singleton\TSingleton;

  protected $var = 1;

  public function setVar($var){
    $this->var = $var;
  }

  public function getVar(){
    return $this->var;
  }
}
