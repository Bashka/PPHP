<?php
namespace PPHP\tests\tools\patterns\database\persistent;

use PPHP\tools\patterns\database\persistent\LongObject;

class LongObjectMock extends LongObject{
  protected function getSavedState(){
    return get_object_vars($this);
  }
}