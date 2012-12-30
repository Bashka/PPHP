<?php
namespace PPHP\tests\tools\classes\standard\fileSystem;

class TestObserver implements \SplObserver{
  private $updating = false;

  /**
   * (PHP 5 &gt;= 5.1.0)<br/>
   * Receive update from subject
   * @link http://php.net/manual/en/splobserver.update.php
   * @param \SplSubject $subject <p>
   * The <b>SplSubject</b> notifying the longObject of an update.
   * </p>
   * @return void
   */
  public function update(\SplSubject $subject){
    $this->updating = true;
  }

  public function getUpdating(){
    return $this->updating;
  }
}
