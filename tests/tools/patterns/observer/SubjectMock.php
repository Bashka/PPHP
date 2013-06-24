<?php
namespace PPHP\tests\tools\patterns\observer;

use PPHP\tools\patterns\observer\TSubject;

class SubjectMock implements \SplSubject{
  use TSubject;

  /**
   * @return \SplObjectStorage
   */
  public function getObservers(){
    return $this->observers;
  }
}