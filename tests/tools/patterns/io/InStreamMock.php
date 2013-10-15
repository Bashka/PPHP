<?php
namespace PPHP\tests\tools\patterns\io;

use PPHP\tools\patterns\io\InStream;

class InStreamMock extends InStream{
  const LENGTH = 52;

  private $content = "First string\r\nВторая строка\r\nLast string";

  private $point = 0;

  public function read(){
    if(!isset($this->content[$this->point])){
      return '';
    }
    else{
      return $this->content[$this->point++];
    }
  }

  public function setPoint($point){
    $this->point = $point;
  }
}
