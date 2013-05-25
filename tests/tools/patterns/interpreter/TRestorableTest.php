<?php
namespace PPHP\tests\tools\patterns\interpreter;
use \PPHP\tests\tools\patterns\interpreter\TRestorableMock;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class TRestorableTest extends \PHPUnit_Framework_TestCase {
  /**
   * @covers TRestorable::reestablish
   */
  public function testReestablish(){
    $obj = TRestorableMock::reestablish('a:1');
    $this->assertEquals(1, $obj->getVar('a'));

    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\StructureException');
    TRestorableMock::reestablish('a');
  }

  /**
   * @covers TRestorable::isReestablish
   */
  public function testisReestablish(){
    $this->assertTrue(TRestorableMock::isReestablish('a:1'));
    $this->assertFalse(TRestorableMock::isReestablish('a:'));
    $this->assertFalse(TRestorableMock::isReestablish(':1'));
    $this->assertFalse(TRestorableMock::isReestablish('a'));
    $this->assertFalse(TRestorableMock::isReestablish(''));
  }
}
