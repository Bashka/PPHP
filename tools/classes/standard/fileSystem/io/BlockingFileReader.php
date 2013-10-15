<?php
namespace PPHP\tools\classes\standard\fileSystem\io;

/**
 * Класс расширяет входной файловый поток, добавляя ему механизмы регистрации и оповещения наблюдателей.
 * Использование шаблона "Наблюдатель" необходимо для информирования целевого файла о закрытии использующих его потоков и отключения блокировки.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\classes\standard\fileSystem\io
 */
class BlockingFileReader extends FileReader implements \SplSubject{
  use \PPHP\tools\patterns\observer\TSubject;

  /**
   * Метод дополнен оповещением наблюдателей о закрытии потока.
   * @prototype \PPHP\tools\patterns\io\Closed
   */
  public function close(){
    $result = parent::close();
    if($result){
      $this->notify();
    }

    return $result;
  }
}
