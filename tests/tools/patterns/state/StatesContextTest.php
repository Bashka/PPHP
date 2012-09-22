<?php
namespace PPHP\tests\tools\patterns\state;

spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});

interface TestContextInterface{
  public function open();

  public function close();
}

class TestContext implements \PPHP\tools\patterns\state\StatesContext, TestContextInterface{
use \PPHP\tools\patterns\state\TStatesContext;

  public function open(){
    return $this->currentState->open();
  }

  public function close(){
    return $this->currentState->close();
  }

  function __construct(){
    $this->statesBuffer = new TestMapBuffer();
    $this->passageState('Close', $this);
  }
}

class TestMapBuffer extends \PPHP\tools\patterns\state\StateBuffer{
  /**
   * Метод служит для запроса данных из первоисточника в случае отсутствия их в буфере.
   * @param string $key
   * @param array|null $arguments
   * @throws \OutOfBoundsException Выбрасывается при попытке полечения несуществующего состояния.
   * @return mixed
   */
  protected function getFromSource($key, array $arguments = null){
    switch($key){
      case 'Open':
        return new TestStateOpen($arguments['context'], $arguments['links']);
        break;
      case 'Close':
        return new TestStateClose($arguments['context'], $arguments['links']);
        break;
      default:
        throw new \OutOfBoundsException('Недопустимый вид состояния.');
    }
  }
}

class TestStateOpen extends \PPHP\tools\patterns\state\State implements TestContextInterface{
  public function open(){
    throw new \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException('Невозможно вызвать данный метод, так как объект не готов к его выполнению');
  }

  public function close(){
    $this->context->passageState('Close', $this);
    return 'close';
  }
}

class TestStateClose extends \PPHP\tools\patterns\state\State implements TestContextInterface{
  public function open(){
    $this->context->passageState('Open', $this);
    return 'open';
  }

  public function close(){
    throw new \PPHP\tools\classes\standard\baseType\exceptions\RuntimeException('Невозможно вызвать данный метод, так как объект не готов к его выполнению');
  }
}

class StatesContextTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var TestContext
   */
  protected $object;

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp(){
    $this->object = new TestContext();
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown(){
  }

  /**
   * @covers PPHP\tools\patterns\state\TStatesContext::getNameCurrentState
   */
  public function testGetNameCurrentState(){
    $this->assertEquals('PPHP\tests\tools\patterns\state\TestStateClose', $this->object->getNameCurrentState());
  }

  /**
   * @covers PPHP\tools\patterns\state\TStatesContext::passageState
   * @covers PPHP\tools\patterns\state\State::__construct
   */
  public function testPassageState(){
    $result = $this->object->open();
    $this->assertEquals('open', $result);
    $this->assertEquals('PPHP\tests\tools\patterns\state\TestStateOpen', $this->object->getNameCurrentState());
  }

  /**
   * @covers PPHP\tools\patterns\state\TStatesContext::passageState
   */
  public function testPassageStateException(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\RuntimeException');
    $this->object->close();
  }
}
