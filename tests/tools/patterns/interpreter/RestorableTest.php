<?php
namespace PPHP\tests\tools\patterns\interpreter;

use PPHP\tests\tools\patterns\interpreter\InterpreterRestorableMock;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class RestorableTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers Restorable::reestablish
   */
  public function testReestablish(){
    $obj = InterpreterRestorableMock::reestablish('InterpreterRestorableMock:1');
    $this->assertEquals(1, $obj->getVar());
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException');
    InterpreterRestorableMock::reestablish('InterpreterRestorableMock');
  }

  /**
   * @covers Restorable::isReestablish
   */
  public function testisReestablish(){
    $this->assertTrue(InterpreterRestorableMock::isReestablish('InterpreterRestorableMock:1'));
    $this->assertFalse(InterpreterRestorableMock::isReestablish('InterpreterRestorableMock:'));
  }
}
