<?php
namespace PPHP\tests\tools\patterns\metadata\reflection;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class ReflectionClassTest extends \PHPUnit_Framework_TestCase{
  /**
   * Должен конвертировать PHPDoc класса в метаданные.
   * @covers PPHP\tools\patterns\metadata\reflection\ReflectionClass::__construct
   */
  public function testShouldAddMetadataFromDoc(){
    $this->assertEquals('testValue', ParentReflectMock::getReflectionClass()->getMetadata('testMetadata'));
    $this->assertTrue(ParentReflectMock::getReflectionClass()->isMetadataExists('testMarker'));
  }

  /**
   * Должен возвращать документацию класса.
   * @covers PPHP\tools\patterns\metadata\reflection\ReflectionClass::getDoc
   */
  public function testShouldReturnDoc(){
    $d = ParentReflectMock::getReflectionClass()->getDoc();
    $this->assertInstanceOf('PPHP\tools\patterns\metadata\reflection\ReflectionDoc', $d);
  }
}
