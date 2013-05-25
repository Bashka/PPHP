<?php
namespace PPHP\tests\tools\patterns\io;
use \PPHP\tools\patterns\io as io;

class InStreamMock extends io\InStream{
  private $descriptor;

  function __construct($descriptor){
    $this->descriptor = $descriptor;
  }

  /**
   * Метод считывает один байт из потока.
   * @throws io\IOException Выбрасывается в случае возникновения ошибки при чтении из потока.
   * @return string Возвращает текущий байт из потока или пустую строку, если поток закончет.
   */
  public function read(){
    $char = fgetc($this->descriptor);
    if($char === false){
      return '';
    }
    else{
      return $char;
    }
  }
}
