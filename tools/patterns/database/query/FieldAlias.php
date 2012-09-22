<?php
namespace PPHP\tools\patterns\database\query;

class FieldAlias extends Alias{
  function __construct($component, $alias){
    if(!($component instanceof Field)){
      throw new \PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException('Field', $component);
    }
    parent::__construct($component, $alias);
  }
}
