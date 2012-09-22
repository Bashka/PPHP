<?php
namespace PPHP\tools\patterns\observer;

/**
 * Данная реализация добавляет механизмы регистрации и оповещения подписчиков.
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

class ExampleObserver implements \SplObserver{
  /**
   * (PHP 5 &gt;= 5.1.0)<br/>
   * Receive update from subject
   * @link http://php.net/manual/en/splobserver.update.php
   * @param \SplSubject $subject <p>
   * The <b>SplSubject</b> notifying the observer of an update.
   * </p>
   * @return void
   */
  public function update(\SplSubject $subject){
    echo 'Реакция подписчика на оповещение';
  }
}