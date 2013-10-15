<?php
namespace PPHP\tests\tools\patterns\state;

use PPHP\tools\patterns\state as state;

class ContextMock implements state\StatesContext, ContextInterfaceMock{
  use state\TStatesContext;

  public function open(){
    return $this->currentState->open();
  }

  public function close(){
    return $this->currentState->close();
  }

  function __construct(){
    $this->statesFactory = new StateBufferMock;
    $this->passageState('Close', $this);
  }
}