<?php
namespace PPHP\tests\tools\patterns\state;

use PPHP\tools\patterns\state as state;

class StateBufferMock extends state\StateBuffer{
  protected function getFromSource($key, array $arguments = null){
    switch($key){
      case 'Open':
        return new StateOpenMock($arguments['context'], $arguments['links']);
        break;
      case 'Close':
        return new StateCloseMock($arguments['context'], $arguments['links']);
        break;
      default:
        throw new \OutOfBoundsException('Недопустимый вид состояния.');
    }
  }
}