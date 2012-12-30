<?php
namespace PPHP\tools\classes\standard\fileSystem\io;

/**
 * Реализация интерфейса SeekIO для файловых потоков.
 */
trait FileSeekIO{
  /**
   * Метод устанавливает указатель символа на указанную позицию.
   * @param integer $position Позиция символа.
   * @return boolean true - если позиция установлена, иначе - false.
   */
  public function setPosition($position){
    return (boolean) (fseek($this->resource, $position) + 1);
  }

  /**
   * Метод возвращает текущую позицию указателя символа.
   * @abstract
   * @return integer
   */
  public function getPosition(){
    return ftell($this->resource);
  }
}
