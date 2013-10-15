<?php
namespace PPHP\tests\tools\patterns\metadata\reflection;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class ReflectionDocTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var \PPHP\tools\patterns\metadata\reflection\ReflectionDoc
   */
  protected $object;

  protected function setUp(){
    $this->object = ChildReflectMock::getReflectionClass()->getDoc();
  }

  /**
   * Должен возвращать описательную часть документации.
   * @covers \PPHP\tools\patterns\metadata\reflection\ReflectionDoc::getDescription
   */
  public function testShouldReturnDescription(){
    $this->assertEquals('Class ChildReflectMock' . "\n" . 'Описание класса.' . "\n", $this->object->getDescription());
  }

  /**
   * Должен возвращать массив значений с указанным тегом.
   * @covers \PPHP\tools\patterns\metadata\reflection\ReflectionDoc::getTag
   */
  public function testShouldReturnValueTag(){
    $this->assertEquals('PPHP\tests\tools\patterns\metadata\reflection', $this->object->getTag('package')[0]);
  }

  /**
   * Должен возвращать true - если указанный тег присутствует в документации, иначе - false.
   * @covers \PPHP\tools\patterns\metadata\reflection\ReflectionDoc::hasTag
   */
  public function testShouldReturnTrueIfTagExists(){
    $this->assertTrue($this->object->hasTag('package'));
    $this->assertFalse($this->object->hasTag('test'));
  }
}
