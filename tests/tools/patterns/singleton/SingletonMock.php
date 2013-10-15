<?php
namespace PPHP\tests\tools\patterns\singleton;

use PPHP\tools\patterns\singleton\Singleton;
use PPHP\tools\patterns\singleton\TSingleton;

class SingletonMock implements Singleton{
  use TSingleton;

  protected $var = 1;

  public function setVar($var){
    $this->var = $var;
  }

  public function getVar(){
    return $this->var;
  }
}
