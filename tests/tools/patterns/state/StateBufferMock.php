<?php
namespace PPHP\tests\tools\patterns\state;

use PPHP\tools\patterns\state\StateCache;

class StateBufferMock extends StateCache{
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