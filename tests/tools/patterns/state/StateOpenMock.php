<?php
namespace PPHP\tests\tools\patterns\state;

use PPHP\tools\classes\standard\baseType\exceptions\RuntimeException;
use PPHP\tools\patterns\state as state;

class StateOpenMock extends state\State implements ContextInterfaceMock{
  public function open(){
    throw new RuntimeException('Невозможно вызвать данный метод, так как объект не готов к его выполнению');
  }

  public function close(){
    $this->context->passageState('Close', $this);

    return 'close';
  }
}