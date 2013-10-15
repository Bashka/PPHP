<?php
namespace PPHP\tests\tools\patterns\metadata\reflection;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class ReflectionPropertyTest extends \PHPUnit_Framework_TestCase{
  /**
   * Должен конвертировать PHPDoc свойства в метаданные.
   * @covers PPHP\tools\patterns\metadata\reflection\ReflectionProperty::__construct
   */
  public function testShouldAddMetadataFromDoc(){
    /**
     * @var \PPHP\tools\patterns\metadata\reflection\ReflectionProperty $r
     */
    $r = ParentReflectMock::getReflectionProperty('a');
    $this->assertEquals('testValue', $r->getMetadata('testMetadata'));
    $this->assertTrue($r->isMetadataExists('testMarker'));
  }

  /**
   * Должен возвращать документацию свойства.
   * @covers PPHP\tools\patterns\metadata\reflection\ReflectionProperty::getDoc
   */
  public function testShouldReturnDoc(){
    /**
     * @var \PPHP\tools\patterns\metadata\reflection\ReflectionProperty $r
     */
    $r = ParentReflectMock::getReflectionProperty('a');
    $d = $r->getDoc();
    $this->assertInstanceOf('PPHP\tools\patterns\metadata\reflection\ReflectionDoc', $d);
  }
}
