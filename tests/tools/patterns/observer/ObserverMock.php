<?php
namespace PPHP\tests\tools\patterns\observer;

class ObserverMock implements \SplObserver{
  public static $state = 0;

  public function update(\SplSubject $subject){
    self::$state++;
  }
}