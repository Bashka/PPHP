<?php
namespace PPHP\tests\tools\patterns\metadata\reflection;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class ReflectionMethodTest extends \PHPUnit_Framework_TestCase{
  /**
   * Должен конвертировать PHPDoc метода в метаданные.
   * @covers PPHP\tools\patterns\metadata\reflection\ReflectionMethod::__construct
   */
  public function testShouldAddMetadataFromDoc(){
    /**
     * @var \PPHP\tools\patterns\metadata\reflection\ReflectionMethod $r
     */
    $r = ParentReflectMock::getReflectionMethod('c');
    $this->assertEquals('testValue', $r->getMetadata('testMetadata'));
    $this->assertTrue($r->isMetadataExists('testMarker'));
  }

  /**
   * Должен возвращать документацию метода.
   * @covers PPHP\tools\patterns\metadata\reflection\ReflectionMethod::getDoc
   */
  public function testShouldReturnDoc(){
    /**
     * @var \PPHP\tools\patterns\metadata\reflection\ReflectionMethod $r
     */
    $r = ParentReflectMock::getReflectionMethod('c');
    $d = $r->getDoc();
    $this->assertInstanceOf('PPHP\tools\patterns\metadata\reflection\ReflectionDoc', $d);
  }

  /**
   * Должен возвращать отражение аргумента по его имени.
   * @covers PPHP\tools\patterns\metadata\reflection\ReflectionMethod::getParameter
   */
  public function testShouldReturnReflectionArgFromName(){
    /**
     * @var \PPHP\tools\patterns\metadata\reflection\ReflectionMethod $r
     */
    $r = ParentReflectMock::getReflectionMethod('c');
    $a = $r->getParameter('x');
    $this->assertInstanceOf('PPHP\tools\patterns\metadata\reflection\ReflectionParameter', $a);
  }

  /**
   * Должен возвращать отражение аргумента по его индексу.
   * @covers PPHP\tools\patterns\metadata\reflection\ReflectionMethod::getParameter
   */
  public function testShouldReturnReflectionArgFromIndex(){
    /**
     * @var \PPHP\tools\patterns\metadata\reflection\ReflectionMethod $r
     */
    $r = ParentReflectMock::getReflectionMethod('c');
    $a = $r->getParameter(0);
    $this->assertInstanceOf('PPHP\tools\patterns\metadata\reflection\ReflectionParameter', $a);
  }

  /**
   * Должен выбрасывать исключение, если запрашиваемого параметра не существует.
   * @covers PPHP\tools\patterns\metadata\reflection\ReflectionMethod::getParameter
   */
  public function testShouldThrowExceptionIfArgNotExists(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\ComponentClassException');
    /**
     * @var \PPHP\tools\patterns\metadata\reflection\ReflectionMethod $r
     */
    $r = ParentReflectMock::getReflectionMethod('c');
    $r->getParameter('y');
  }
}
