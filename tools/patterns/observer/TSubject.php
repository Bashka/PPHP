<?php
namespace PPHP\tools\patterns\observer;

/**
 * Классическая реализация интерфейса SplObserver.
 * @author Artur Sh. Mamedbekov
 * @package PPHP\tools\patterns\observer
 */
trait TSubject{
  /**
   * Хранилище подписчиков.
   * @var \SplObjectStorage
   */
  private $observers;

  /**
   * (PHP 5 &gt;= 5.1.0)<br/>
   * Attach an SplObserver
   * @link http://php.net/manual/en/splsubject.attach.php
   * @param \SplObserver $observer <p>
   * The <b>SplObserver</b> to attach.
   * </p>
   * @return void
   */
  public function attach(\SplObserver $observer){
    if(empty($this->observers)){
      $this->observers = new \SplObjectStorage();
    }
    $this->observers->attach($observer);
  }

  /**
   * (PHP 5 &gt;= 5.1.0)<br/>
   * Detach an longObject
   * @link http://php.net/manual/en/splsubject.detach.php
   * @param \SplObserver $observer <p>
   * The <b>SplObserver</b> to detach.
   * </p>
   * @return void
   */
  public function detach(\SplObserver $observer){
    if(empty($this->observers)){
      $this->observers = new \SplObjectStorage();
    }
    $this->observers->detach($observer);
  }

  /**
   * (PHP 5 &gt;= 5.1.0)<br/>
   * Notify an longObject
   * @link http://php.net/manual/en/splsubject.notify.php
   * @return void
   */
  public function notify(){
    if(empty($this->observers)){
      $this->observers = new \SplObjectStorage();
    }
    foreach($this->observers as $observer){
      $observer->update($this);
    }
  }
}