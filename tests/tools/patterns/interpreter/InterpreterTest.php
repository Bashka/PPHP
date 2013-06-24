<?php
namespace PPHP\tests\tools\patterns\interpreter;

use PPHP\tests\tools\patterns\interpreter\InterpreterRestorableMock;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class InterpreterTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var InterpreterRestorableMock
   */
  protected $object;

  protected function setUp(){
    $this->object = new InterpreterRestorableMock();
  }

  /**
   * @covers Interpreter::interpretation
   */
  public function testInterpretation(){
    $this->assertEquals('InterpreterRestorableMock:1', $this->object->interpretation());
    $this->object->setVar(2);
    $this->assertEquals('InterpreterRestorableMock:2', $this->object->interpretation());
  }
}
