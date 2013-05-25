<?php
namespace PPHP\tests\tools\patterns\metadata;
use PPHP\tools\patterns\metadata\reflection as reflection;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class TestReflectTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var TestReflect
   */
  static protected $object;
  /**
   * @var ParentReflectMock
   */
  static protected $parentClass;

  public static function setUpBeforeClass(){
    self::$object = new TestReflect();
    self::$parentClass = new ParentReflectMock();
  }

  /**
   * @covers reflection\TReflect::getReflectionProperty
   * @covers reflection\TReflect::getReflectionMethod
   * @covers reflection\TReflect::getReflectionClass
   */
  public function testGetReflection(){
    $reflectionProperty = self::$object->getReflectionProperty('a');
    $reflectionMethod = self::$object->getReflectionMethod('c');
    $reflectionClass = self::$object->getReflectionClass();

    $this->assertEquals($reflectionProperty, self::$object->getReflectionProperty('a'));
    $this->assertEquals($reflectionMethod, self::$object->getReflectionMethod('c'));
    $this->assertEquals($reflectionClass, self::$object->getReflectionClass());

    $this->assertTrue($reflectionProperty === self::$parentClass->getReflectionProperty('a'));
    $this->assertTrue($reflectionMethod === self::$parentClass->getReflectionMethod('c'));
    $this->assertFalse($reflectionClass === self::$parentClass->getReflectionClass());
  }

  /**
   * @covers reflection\TReflect::getReflectionProperty
   */
  public function testGetReflectionProperty(){
    $reflectionProperty = self::$object->getReflectionProperty('e');
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\reflection\ReflectionProperty', $reflectionProperty);
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\Described', $reflectionProperty);

    $reflectionStaticProperty = self::$object->getReflectionProperty('g');
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\reflection\ReflectionProperty', $reflectionStaticProperty);
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\Described', $reflectionStaticProperty);

    $reflectionProperty = self::$object->getReflectionProperty('a');
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\reflection\ReflectionProperty', $reflectionProperty);
    $this->assertEquals('PPHP\tests\tools\patterns\metadata\ParentReflectMock', $reflectionProperty->getDeclaringClass()->getName());

    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\ComponentClassException');
    $this->assertNull(self::$object->getReflectionProperty('k'));
  }

  /**
   * @covers reflection\TReflect::getReflectionMethod
   */
  public function testGetReflectionMethod(){
    $reflectionMethod = self::$object->getReflectionMethod('h');
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\reflection\ReflectionMethod', $reflectionMethod);
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\Described', $reflectionMethod);

    $reflectionStaticMethod = self::$object->getReflectionMethod('j');
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\reflection\ReflectionMethod', $reflectionStaticMethod);
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\Described', $reflectionStaticMethod);

    $reflectionMethod = self::$object->getReflectionMethod('c');
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\reflection\ReflectionMethod', $reflectionMethod);
    $this->assertEquals('PPHP\tests\tools\patterns\metadata\ParentReflectMock', $reflectionMethod->getDeclaringClass()->getName());

    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\ComponentClassException');
    $this->assertNull(self::$object->getReflectionMethod('k'));
  }

  /**
   * @covers reflection\TReflect::getReflectionClass
   */
  public function testGetReflectionClass(){
    $reflectionClass = self::$object->getReflectionClass();
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\reflection\ReflectionClass', $reflectionClass);
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\Described', $reflectionClass);
  }

  /**
   * @covers reflection\TReflect::getAllReflectionProperties
   */
  public function testGetAllReflectionProperties(){
    $ps = self::$object->getAllReflectionProperties();
    $this->assertEquals('e', $ps['e']->getName());
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\reflection\ReflectionProperty', $ps['e']);
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\Described', $ps['e']);
    $this->assertEquals('a', $ps['a']->getName());
  }

  /**
   * @covers reflection\TReflect::getAllReflectionMethods
   */
  public function testGetAllReflectionMethods(){
    $ms = self::$object->getAllReflectionMethods();
    $this->assertEquals('h', $ms['h']->getName());
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\reflection\ReflectionMethod', $ms['h']);
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\Described', $ms['h']);
    $this->assertEquals('c', $ms['c']->getName());
  }

  /**
   * @covers PPHP\tools\patterns\metadata\TDescribed::getAllMetadata
   */
  public function testGetAllMetadata(){
    $reflectionProperty = self::$object->getReflectionProperty('a');
    $this->assertEquals(2, count($reflectionProperty->getAllMetadata()));
  }

  /**
   * @covers PPHP\tools\patterns\metadata\TDescribed::getMetadata
   * @covers PPHP\tools\patterns\metadata\TDescribed::setMetadata
   * @covers PPHP\tools\patterns\metadata\reflection\ReflectionClass::__construct
   * @covers PPHP\tools\patterns\metadata\reflection\ReflectionModule::__construct
   */
  public function testGetMetadata(){
    $this->assertEquals('Test', self::$object->getReflectionProperty('e')->getMetadata('Metadata'));
    $this->assertEquals('TestParent', self::$parentClass->getReflectionProperty('a')->getMetadata('Metadata'));

    $this->assertEquals('testValue', ParentReflectMock::getReflectionClass()->getMetadata('testMetadata'));
    $this->assertEquals('testValue', ParentReflectMock::getReflectionMethod('c')->getMetadata('testMetadata'));
  }

  /**
   * @covers PPHP\tools\patterns\metadata\TDescribed::isMetadataExists
   * @covers PPHP\tools\patterns\metadata\reflection\ReflectionProperty::__construct
   */
  public function testIsMetadataExists(){
    $reflectionProperty = self::$object->getReflectionProperty('a');
    $this->assertTrue($reflectionProperty->isMetadataExists('Metadata'));

    $this->assertTrue(ParentReflectMock::getReflectionProperty('a')->isMetadataExists('testMetadata'));
  }
}
