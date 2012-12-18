<?php
namespace PPHP\tools\classes\standard\baseType\exceptions;

/**
 * Выбрасывается при передаче параметра неверного типа.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\exceptions
 */
class InvalidArgumentException extends LogicException{
  /**
   * @param string|null $expectedType Ожидаемый тип параметра.
   * @param mixed|null $actualData Реальное значение параметра.
   * @param int $code Код ошибки.
   * @param null|Exception $previous Причина.
   */
  public function __construct ($expectedType=null, $actualData=null, $code = 0, Exception $previous = null) {
    if(is_null($expectedType) && is_null($actualData)){
      parent::__construct('Недопустимое значение аргумента.', $code, $previous);
    }
    elseif($expectedType !== null && $actualData === null){
      parent::__construct($expectedType, $code, $previous);
    }
    else{
      parent::__construct('Неверный тип аргумента, ожидается '.$expectedType.' вместо '.gettype($actualData).'.', $code, $previous);
    }
  }
}
