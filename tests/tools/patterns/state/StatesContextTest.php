<?php
namespace PPHP\tests\tools\patterns\state;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class StatesContextTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var ContextMock
   */
  protected $object;

  protected function setUp(){
    $this->object = new ContextMock;
  }

  /**
   * @covers PPHP\tools\patterns\state\TStatesContext::getNameCurrentState
   */
  public function testGetNameCurrentState(){
    $this->assertEquals('PPHP\tests\tools\patterns\state\StateCloseMock', $this->object->getNameCurrentState());
  }

  /**
   * @covers PPHP\tools\patterns\state\TStatesContext::passageState
   * @covers PPHP\tools\patterns\state\State::__construct
   */
  public function testPassageState(){
    $result = $this->object->open();
    $this->assertEquals('open', $result);
    $this->assertEquals('PPHP\tests\tools\patterns\state\StateOpenMock', $this->object->getNameCurrentState());
  }

  /**
   * @covers PPHP\tools\patterns\state\TStatesContext::passageState
   */
  public function testPassageStateException(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\RuntimeException');
    $this->object->close();
  }
}
