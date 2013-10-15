<?php
namespace PPHP\tests\tools\patterns\observer;

use PPHP\tools\patterns\observer\TSubject;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class SubjectTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var SubjectMock
   */
  protected $object;

  protected function setUp(){
    $this->object = new SubjectMock;
  }

  /**
   * Должен добавлять подписчика в список слушателей.
   * @covers \PPHP\tools\patterns\observer\TSubject::attach
   */
  public function testShouldAddObserverInListenersList(){
    $o = new ObserverMock;
    $this->object->attach($o);
    $ol = $this->object->getObservers();
    $this->assertEquals(1, $ol->count());
    $ol->rewind();
    $this->assertEquals($o, $ol->current());
  }

  /**
   * Должен предотвращать повторное добавление подписчика в список слушателей.
   * @covers \PPHP\tools\patterns\observer\TSubject::attach
   */
  public function testShouldPreventDuplication(){
    $o = new ObserverMock;
    $this->object->attach($o);
    $this->object->attach($o);
    $ol = $this->object->getObservers();
    $this->assertEquals(1, $ol->count());
    $ol->rewind();
    $this->assertEquals($o, $ol->current());
  }

  /**
   * Должен удалять подписчика из списка слушателей.
   * @covers \PPHP\tools\patterns\observer\TSubject::detach
   */
  public function testShouldRemoveObserverFromListenersList(){
    $o = new ObserverMock;
    $this->object->attach($o);
    $this->object->detach($o);
    $ol = $this->object->getObservers();
    $this->assertEquals(0, $ol->count());
  }

  /**
   * Не должен реагировать при отсутствии указанного подписчика в листе слушателей.
   * @covers \PPHP\tools\patterns\observer\TSubject::detach
   */
  public function testShouldBeSilentIfObserverNotFound(){
    $o = new ObserverMock;
    $this->object->detach($o);
  }

  /**
   * Должен информировать всех подписчиков в листе слушателей.
   * @covers \PPHP\tools\patterns\observer\TSubject::notify
   */
  public function testShouldNotifyWholeListenersList(){
    $this->object->attach(new ObserverMock);
    $this->object->attach(new ObserverMock);
    $this->object->attach(new ObserverMock);
    $this->object->notify();
    $this->assertEquals(3, ObserverMock::$state);
  }
}
