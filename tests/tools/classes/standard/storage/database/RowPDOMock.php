<?php
namespace PPHP\tests\tools\classes\standard\storage\database;


class RowPDOMock{
  public $rowCount = 0;

  public $data = [];

  public function rowCount(){
    return $this->rowCount;
  }

  public function fetch($x = null){
    return array_shift($this->data);
  }
}