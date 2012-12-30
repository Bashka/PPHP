<?php
namespace PPHP\tests\tools\patterns\metadata;

spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-06-24 at 15:24:01.
 */
class TestReflectTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var TestReflect
   */
  static protected $object;
  /**
   * @var TestParentReflect
   */
  static protected $parentClass;

  public static function setUpBeforeClass(){
    self::$object = new TestReflect();
    self::$parentClass = new TestParentReflect();
  }

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   */
  protected function setUp(){
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   */
  protected function tearDown(){
  }

  /**
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getReflectionProperty
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getReflectionMethod
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getReflectionClass
   */
  public function testGetReflection(){
    $reflectionProperty = self::$object->getReflectionProperty('prop');
    $reflectionMethod = self::$object->getReflectionMethod('method');
    $reflectionClass = self::$object->getReflectionClass();

    $this->assertEquals($reflectionProperty, self::$object->getReflectionProperty('prop'));
    $this->assertEquals($reflectionMethod, self::$object->getReflectionMethod('method'));
    $this->assertEquals($reflectionClass, self::$object->getReflectionClass());

    $this->assertFalse($reflectionProperty === self::$parentClass->getReflectionProperty('prop'));
    $this->assertFalse($reflectionMethod === self::$parentClass->getReflectionMethod('method'));
    $this->assertFalse($reflectionClass === self::$parentClass->getReflectionClass());
  }

  /**
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getReflectionProperty
   */
  public function testGetReflectionProperty(){
    $reflectionProperty = self::$object->getReflectionProperty('prop');
    $this->assertInstanceOf('\ReflectionProperty', $reflectionProperty);
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\Described', $reflectionProperty);

    $reflectionStaticProperty = self::$object->getReflectionProperty('propStatic');
    $this->assertInstanceOf('\ReflectionProperty', $reflectionStaticProperty);
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\Described', $reflectionStaticProperty);
  }

  /**
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getReflectionMethod
   */
  public function testGetReflectionMethod(){
    $reflectionMethod = self::$object->getReflectionMethod('method');
    $this->assertInstanceOf('\ReflectionMethod', $reflectionMethod);
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\Described', $reflectionMethod);

    $reflectionStaticMethod = self::$object->getReflectionMethod('methodStatic');
    $this->assertInstanceOf('\ReflectionMethod', $reflectionStaticMethod);
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\Described', $reflectionStaticMethod);
  }

  /**
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getReflectionClass
   */
  public function testGetReflectionClass(){
    $reflectionClass = self::$object->getReflectionClass();
    $this->assertInstanceOf('\ReflectionClass', $reflectionClass);
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\Described', $reflectionClass);
  }

  /**
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getAllReflectionProperties
   */
  public function testGetAllReflectionProperties(){
    $this->assertEquals(7, self::$object->getAllReflectionProperties()->count());
  }

  /**
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getAllReflectionMethods
   */
  public function testGetAllReflectionMethods(){
    $this->assertEquals(9, self::$object->getAllReflectionMethods()->count());
  }

  /**
   * @covers PPHP\tools\patterns\metadata\TDescribed::getAllMetadata
   */
  public function testGetAllMetadata(){
    $reflectionProperty = self::$object->getReflectionProperty('prop');
    $this->assertEquals(1, count($reflectionProperty->getAllMetadata()));
  }

  /**
   * @covers PPHP\tools\patterns\metadata\TDescribed::getMetadata
   * @covers PPHP\tools\patterns\metadata\TDescribed::setMetadata
   */
  public function testGetMetadata(){
    $this->assertEquals('Test', self::$object->getReflectionProperty('prop')->getMetadata('Metadata'));
    $this->assertEquals('TestParent', self::$parentClass->getReflectionProperty('prop')->getMetadata('Metadata'));
  }

  /**
   * @covers PPHP\tools\patterns\metadata\TDescribed::isMetadataExists
   */
  public function testIsMetadataExists(){
    $reflectionProperty = self::$object->getReflectionProperty('prop');
    $this->assertTrue($reflectionProperty->isMetadataExists('Metadata'));
  }
}
