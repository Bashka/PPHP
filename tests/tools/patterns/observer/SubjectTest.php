<?php
namespace PPHP\tests\tools\patterns\observer;

use PPHP\tools\patterns\observer\TSubject;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class SubjectTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var SubjectMock
   */
  protected $subject;

  protected function setUp(){
    $this->subject = new SubjectMock;
  }

  /**
   * @covers TSubject::attach
   */
  public function testAttach(){
    $this->subject->attach(new ObserverMock);
    $this->assertEquals(1, $this->subject->getObservers()->count());
  }

  /**
   * @covers TSubject::detach
   */
  public function testDetach(){
    $observer = new ObserverMock;
    $this->subject->attach($observer);
    $this->subject->detach($observer);
    $this->assertEquals(0, $this->subject->getObservers()->count());
  }

  /**
   * @covers TSubject::notify
   */
  public function testNotify(){
    $this->subject->attach(new ObserverMock);
    $this->subject->attach(new ObserverMock);
    $this->subject->notify();
    $this->assertEquals(2, ObserverMock::$state);
  }
}
