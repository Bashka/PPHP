<?php
namespace PPHP\tests\tools\patterns\database\persistent;

use PPHP\tools\patterns\database\persistent\LongObject;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class LongObjectTest extends \PHPUnit_Framework_TestCase{
  /**
   * Должен восстанавливать объект из строки вида: $/имяКласса:идентификатор.
   * @covers \PPHP\tools\patterns\database\persistent\LongObject::reestablish
   */
  public function testShouldReestablishForString(){
    $o = LongObjectMock::reestablish('$/PPHP/tests/tools/patterns/database/persistent/LongObjectMock:1');
    $this->assertInstanceOf('PPHP\tests\tools\patterns\database\persistent\LongObjectMock', $o);
    $this->assertEquals(1, $o->getOID());
  }

  /**
   * Должен выбрасывать исключение при ошибочной формате строки.
   * @covers \PPHP\tools\patterns\database\persistent\LongObject::reestablish
   */
  public function testShouldThrowExceptionForBadString(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\StructureException');
    LongObjectMock::reestablish('$\PPHP\tests\tools\patterns\database\persistent\LongObjectMock:1');
    LongObjectMock::reestablish('$/PPHP/tests/tools/patterns/database/persistent/LongObjectMock:');
    LongObjectMock::reestablish('$/PPHP/tests/tools/patterns/database/persistent/LongObjectMock1');
    LongObjectMock::reestablish('$/PPHP/tests/tools/patterns/database/persistent/LongObjectMock:-1');
    LongObjectMock::reestablish('$/PPHP/tests/tools/patterns/database/persistent/LongObjectMock:0');
  }

  /**
   * Должен интерпретировать в строку вида: $/имяКласса:идентификатор.
   * @covers \PPHP\tools\patterns\database\persistent\LongObject::interpreter
   */
  public function testShouldInterpreterInString(){
    $this->assertEquals('$/PPHP/tests/tools/patterns/database/persistent/LongObjectMock:1', LongObjectMock::getProxy(1)->interpretation());
  }

  /**
   * Должен выбрасывать исключение, если не идентифицирован.
   * @covers \PPHP\tools\patterns\database\persistent\LongObject::interpreter
   */
  public function testShouldThrowExceptionIfNoIdentify(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException');
    $o = new LongObjectMock;
    $o->interpretation();
  }
}
