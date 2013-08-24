<?php
namespace PPHP\tools\patterns\observer;

/**
 * Классическая реализация интерфейса \SplSubject.
 * Данный trait позволяет реализовать логику "Издателя" для класса, оповещающего слушателей об изменении своего состояния.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\observer
 */
trait TSubject{
  /**
   * @var \SplObjectStorage Хранилище подписчиков.
   */
  private $observers;

  /**
   * Метод добавляет подписчика в список слушателей данного издателя.
   * @link http://php.net/manual/en/splsubject.attach.php
   * @param \SplObserver $observer Добавляемый подписчик.
   */
  public function attach(\SplObserver $observer){
    if(empty($this->observers)){
      $this->observers = new \SplObjectStorage();
    }
    $this->observers->attach($observer);
  }

  /**
   * Метод удаляет подписчика из списка слушателей данного издателя.
   * @link http://php.net/manual/en/splsubject.detach.php
   * @param \SplObserver $observer Удаляемый подписчик.
   */
  public function detach(\SplObserver $observer){
    if(empty($this->observers)){
      $this->observers = new \SplObjectStorage();
    }
    $this->observers->detach($observer);
  }

  /**
   * Метод оповещает подписчиков об изменении состояния данного издателя.
   * @link http://php.net/manual/en/splsubject.notify.php
   */
  public function notify(){
    if(empty($this->observers)){
      $this->observers = new \SplObjectStorage();
    }
    /**
     * @var \SplObserver $observer
     */
    foreach($this->observers as $observer){
      /**
       * @var \SplSubject $this
       */
      $observer->update($this);
    }
  }
}