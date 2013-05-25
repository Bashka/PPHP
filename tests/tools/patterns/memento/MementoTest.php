<?php
namespace PPHP\tests\tools\patterns\memento;
use \PPHP\tools\patterns\memento as memento;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class MementoTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var memento\Memento
   */
  protected $memento;

  /**
   * @var TestOriginator
   */
  protected $originator;

  protected function setUp(){
    $this->originator = new TestOriginator();
    $this->memento = $this->originator->createMemento();
  }


  /**
   * @covers memento\TOriginator::createMemento
   * @covers memento\Memento::__construct
   */
  public function testCreateMemento(){
    $this->assertInstanceOf('\PPHP\tools\patterns\memento\Memento', $this->originator->createMemento());
  }

  /**
   * @covers memento\TOriginator::restoreFromMemento
   */
  public function testRestoreFromMemento(){
    $this->originator->setTestVar(3);
    $this->originator->restoreFromMemento($this->memento);
    $this->assertEquals(5, $this->originator->getTestVar());
  }

  /**
   * @covers memento\Memento::getState
   */
  public function testGetState(){
    $this->assertEquals(['testVar' => 5], $this->memento->getState($this->originator));
  }

  /**
   * @covers memento\Memento::getState
   */
  public function testGetStateIfNonOwner(){
    $this->setExpectedException('\PPHP\tools\patterns\memento\AccessException');
    $this->memento->getState(new TestOriginator());
  }
}
