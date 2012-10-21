<?php
namespace PPHP\tools\classes\standard\baseType\exceptions;

class Exception extends \Exception implements \JsonSerializable{
  public function JsonSerialize(){
    return ['type' => get_called_class(), 'message' => $this->message, 'code' => $this->code, 'file' => $this->file, 'line' => $this->line, 'trace' => $this->getTrace()];
  }
}
