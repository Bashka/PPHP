<?php
namespace PPHP\tools\classes\standard\baseType\exceptions;

class InvalidArgumentException extends LogicException{
  public function __construct ($expectedType=null, $actualData=null, $code = 0, Exception $previous = null) {
    if(is_null($expectedType) && is_null($actualData)){
      parent::__construct('Недопустимое значение аргумента.', $code, $previous);
    }
    else{
      parent::__construct('Неверный тип аргумента, ожидается '.$expectedType.' вместо '.gettype($actualData).'.', $code, $previous);
    }
  }
}
