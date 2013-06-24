<?php
namespace PPHP\tools\classes\standard\storage\database\gateway;

class Row{
  protected $table;

  function __construct(Table $table){
    $this->table = $table;
  }

  public function save(){
  }

  public function delete(){
  }

  public function insert(){
  }
}