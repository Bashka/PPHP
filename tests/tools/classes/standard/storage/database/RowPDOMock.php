<?php
namespace PPHP\tests\tools\classes\standard\storage\database;

class RowPDOMock{
  public $data = [];

  public function __construct(array $data = []){
    $this->data = $data;
  }

  public function rowCount(){
    return count($this->data);
  }

  public function fetch($x = null){
    return array_shift($this->data);
  }
}