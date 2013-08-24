<?php
namespace PPHP\tools\classes\standard\fileSystem\io;

use \PPHP\tools\patterns\io as io;

/**
 * Реализация интерфейса SeekIO для файловых потоков.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\fileSystem\io
 */
trait FileSeekIO{
  /**
   * Метод устанавливает указатель символа на указанную позицию.
   * @param integer $position Позиция символа.
   * @throws io\IOException Выбрасывается в случае ошибки при работе с потоком.
   * @return boolean true - если позиция установлена, иначе - false.
   */
  public function setPosition($position){
    return (boolean) (fseek($this->resource, $position) + 1);
  }

  /**
   * Метод возвращает текущую позицию указателя символа.
   * @throws io\IOException Выбрасывается в случае ошибки при работе с потоком.
   * @return integer Позиция указателя в виде целого числа.
   */
  public function getPosition(){
    $result = ftell($this->resource);
    if($result === false){
      throw new io\IOException('Ошибка использования потока ввода.');
    }

    return $result;
  }
}
