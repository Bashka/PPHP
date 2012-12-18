<?php
namespace PPHP\tests\tools\patterns\io;

class InStreamMock extends \PPHP\tools\patterns\io\InStream{
  private $descriptor;

  function __construct($descriptor){
    $this->descriptor = $descriptor;
  }

  /**
   * Метод считывает один байт из потока.
   * @throws \PPHP\tools\patterns\io\IOException Выбрасывается в случае возникновения ошибки при чтении из потока.
   * @return string|boolean Возвращает текущий байт из потока или false, если поток закончет.
   */
  public function read(){
    $char = fgetc($this->descriptor);
    if($char === false){
      return false;
    }
    else{
      return $char;
    }
  }
}
