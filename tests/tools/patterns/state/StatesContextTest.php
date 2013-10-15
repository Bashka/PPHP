<?php
namespace PPHP\tests\tools\patterns\state;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class StatesContextTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var ContextMock
   */
  protected $object;

  protected function setUp(){
    $this->object = new ContextMock;
  }

  /**
   * Должен изменять состояние контекста.
   * @covers PPHP\tools\patterns\state\TStatesContext::passageState
   */
  public function testShouldChangeState(){
    $result = $this->object->open();
    $this->assertEquals('open', $result);
    $this->assertEquals('PPHP\tests\tools\patterns\state\StateOpenMock', $this->object->getNameCurrentState());
  }

  /**
   * Должен контролировать переход состояния.
   * @covers PPHP\tools\patterns\state\TStatesContext::passageState
   */
  public function testShouldChangeStateControl(){
    $result = $this->object->open();
    $this->assertEquals('open', $result);
    $this->assertEquals('PPHP\tests\tools\patterns\state\StateOpenMock', $this->object->getNameCurrentState());
  }

  /**
   * Должен возвращать имя текущего состояния.
   * @covers PPHP\tools\patterns\state\TStatesContext::getNameCurrentState
   */
  public function testShouldReturnNameCurrentState(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\RuntimeException');
    $this->object->close();
  }
}
