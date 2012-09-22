<?php
namespace PPHP\tools\classes\standard\baseType\exceptions;

class Exception extends \Exception implements \JsonSerializable{
  public function __construct($message = "", $code = 0, Exception $previous = null){
    parent::__construct($message, $code, $previous);
    \PPHP\services\log\LogManager::getInstance()->setMessage(\PPHP\services\log\Message::createError($this->getMessage(), $this));
  }

  public function JsonSerialize(){
    return ['type' => get_called_class(), 'message' => $this->message, 'code' => $this->code, 'file' => $this->file, 'line' => $this->line, 'trace' => $this->getTrace()];
  }
}
