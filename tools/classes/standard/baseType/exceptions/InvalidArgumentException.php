<?php
namespace PPHP\tools\classes\standard\baseType\exceptions;

/**
 * Выбрасывается при передаче параметра неверного типа.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\exceptions
 */
class InvalidArgumentException extends LogicException{
  /**
   * Констуктор автоматически дополняет сообщение об ошибке в зависимости от переданных аргументов.
   * Так, если два первых параметра не переданы, констуктор использует стандартное сообщение об ошибке "Недопустимое значение аргумента".
   * Если же первый параметр передан, а второй нет, то в качестве сообщения об ошибке используется строка первого параметра.
   * В случае, если оба параметра переданы, сообщение об ошибке будет сформировано в соответствии со следующим шаблоном: "Неверный тип аргумента, ожидается <первый параметр> вместо <тип второго параметра>.
   *
   * @param string $expectedType [optional] Ожидаемый тип параметра.
   * @param mixed $actualData [optional] Реальное значение параметра.
   * @param int $code [optional] Код ошибки.
   * @param Exception $previous [optional] Причина.
   */
  public function __construct ($expectedType=null, $actualData=null, $code = 0, Exception $previous = null) {
    if(is_null($expectedType) && is_null($actualData)){
      parent::__construct('Недопустимое значение аргумента.', $code, $previous);
    }
    elseif(!is_null($expectedType) && is_null($actualData)){
      parent::__construct($expectedType, $code, $previous);
    }
    else{
      parent::__construct('Неверный тип аргумента, ожидается '.$expectedType.' вместо '.gettype($actualData).'.', $code, $previous);
    }
  }
}
