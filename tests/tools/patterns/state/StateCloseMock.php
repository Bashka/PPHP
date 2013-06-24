<?php
namespace PPHP\tests\tools\patterns\state;

use PPHP\tools\classes\standard\baseType\exceptions\RuntimeException;
use PPHP\tools\patterns\state as state;

class StateCloseMock extends state\State implements ContextInterfaceMock{
  public function open(){
    $this->context->passageState('Open', $this);

    return 'open';
  }

  public function close(){
    throw new RuntimeException('Невозможно вызвать данный метод, так как объект не готов к его выполнению');
  }
}