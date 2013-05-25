<?php
namespace PPHP\tests\tools\patterns\database;
use PPHP\tests\tools\classes\standard\storage\database\ORM\ParentMock;
use PPHP\tools\patterns\database\LongObject;

spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class LongObjectTest extends \PHPUnit_Framework_TestCase {
  /**
   * @covers LongObject::interpretation
   */
  public function testInterpretation(){
    $this->assertEquals('$/PPHP/tests/tools/classes/standard/storage/database/ORM/ParentMock:1', ParentMock::getProxy(1)->interpretation());
  }

  /**
   * @covers LongObject::reestablish
   */
  public function testReestablish(){
    $o = ParentMock::reestablish('$/PPHP/tests/tools/classes/standard/storage/database/ORM/ParentMock:1');
    $this->assertEquals(1, $o->getOID());
  }

  /**
   * @covers LongObject::isReestablish
   */
  public function testIsReestablish(){
    $this->assertTrue(LongObject::isReestablish('$/PPHP/tests/tools/classes/standard/storage/database/ORM/ParentMock:1'));
    $this->assertTrue(LongObject::isReestablish('$ParentMock:1'));
    $this->assertTrue(LongObject::isReestablish('$/PPHP/tests/tools/classes/standard/storage/database/ORM/ParentMock:15'));

    $this->assertFalse(LongObject::isReestablish('/PPHP/tests/tools/classes/standard/storage/database/ORM/ParentMock:1'));
    $this->assertFalse(LongObject::isReestablish('$/PPHP/tests/tools/classes/standard/storage/database/ORM/ParentMock:1as'));
    $this->assertFalse(LongObject::isReestablish('$/PPHP/tests/tools/classes/standard/storage/database/ORM/ParentMock1'));
    $this->assertFalse(LongObject::isReestablish('$/PPHP/tests/tools/classes/standard/storage/database/ORM/ParentMock:'));
    $this->assertFalse(LongObject::isReestablish('$/PPHP/tests/tools/classes/standard/storage/database/ORM/ParentMock:-1'));
  }
}
