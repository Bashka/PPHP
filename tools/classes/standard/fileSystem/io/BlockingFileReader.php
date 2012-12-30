<?php
namespace PPHP\tools\classes\standard\fileSystem\io;

/**
 * Класс расширяет входной поток файла, добавляя ему механизмы регистрации оповещения наблюдателей.
 */
class BlockingFileReader extends FileReader implements \SplSubject{
use \PPHP\tools\patterns\observer\TSubject;

  /**
   * Метод дополнен оповещением подписчиков о закрытии потока.
   * @return boolean true - если компонент был закрыт, иначе - false.
   */
  public function close(){
    $result = parent::close();
    if($result){
      $this->notify();
    }
    return $result;
  }
}
