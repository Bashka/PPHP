<?php
namespace PPHP\tests\tools\patterns\memento;
spl_autoload_register(function($className){
  $root = 'C:/WebServers/home/dic/www';
  require_once $root . '/' . str_replace('\\', '/', $className) . '.php';
});

class TestOriginator implements \PPHP\tools\patterns\memento\Originator{
use \PPHP\tools\patterns\memento\TOriginator;

  private $testVar = 5;


  public function getTestVar(){
    return $this->testVar;
  }

  public function setTestVar($testVar){
    $this->testVar = $testVar;
  }
}
