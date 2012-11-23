<?php
namespace PPHP\tools\classes\standard\baseType\exceptions;

/**
 * Корневой класс исключений системы.
 *
 * Все используемые в системе исключения должны являться дочерними по отношению к данному классу.
 * Дочерние классы исключений могут быть сериализованы для передачи на уровень представления.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\baseType\exceptions
 */
class Exception extends \Exception implements \JsonSerializable{
  public function JsonSerialize(){
    $trace = $this->getTrace();
    foreach($trace as $k => $exc){
      $trace[$k] = new \stdClass();
      $trace[$k]->file = $exc['file'];
      $trace[$k]->line = $exc['line'];
    }
    return ['type' => get_called_class(), 'message' => $this->message, 'code' => $this->code, 'file' => $this->file, 'line' => $this->line, 'trace' => $trace];
  }
}
