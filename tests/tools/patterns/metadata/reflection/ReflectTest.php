<?php
namespace PPHP\tests\tools\patterns\metadata\reflection;

use PPHP\tools\patterns\metadata\reflection as reflection;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class ReflectTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var ChildReflectMock
   */
  static protected $child;

  /**
   * @var ParentReflectMock
   */
  static protected $parent;

  public static function setUpBeforeClass(){
    self::$child = new ChildReflectMock;
    self::$parent = new ParentReflectMock;
  }

  /**
   * Должен возвращать отражение вызываемого класса.
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getReflectionClass
   */
  public function testShouldReturnReflectionClass(){
    $rc = self::$parent->getReflectionClass();
    $this->assertInstanceOf('PPHP\tools\patterns\metadata\reflection\ReflectionClass', $rc);
    $this->assertEquals('PPHP\tests\tools\patterns\metadata\reflection\ParentReflectMock', $rc->getName());
  }

  /**
   * При повторном использовании должен возвращать ранее возвращенное отражение.
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getReflectionClass
   */
  public function testShouldReturnUniqueReflectionClass(){
    $rc = self::$parent->getReflectionClass();
    $this->assertEquals(self::$parent->getReflectionClass(), $rc);
  }

  /**
   * Должен возвращать отражение родительского класса.
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getParentReflectionClass
   */
  public function testShouldReturnReflectionParentClass(){
    $rc = self::$child->getParentReflectionClass();
    $this->assertInstanceOf('PPHP\tools\patterns\metadata\reflection\ReflectionClass', $rc);
    $this->assertEquals('PPHP\tests\tools\patterns\metadata\reflection\ParentReflectMock', $rc->getName());
  }

  /**
   * Должен возвращать null, если вызываемый класс является вершиной наследования.
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getParentReflectionClass
   */
  public function testShouldReturnNullIfClassRoot(){
    $this->assertEquals(null, self::$parent->getParentReflectionClass());
  }

  /**
   * При повторном вызове должен возвращать ранее возращенное отражение.
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getParentReflectionClass
   */
  public function testShouldReturnUniqueReflectionParentClass(){
    $rc = self::$child->getParentReflectionClass();
    $this->assertEquals(self::$child->getParentReflectionClass(), $rc);
  }

  /**
   * Должен возвращать отражение свойства класса.
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getReflectionProperty
   */
  public function testShouldReturnReflectionProperty(){
    /**
     * @var \PPHP\tools\patterns\metadata\reflection\ReflectionProperty $rp
     */
    $rp = self::$parent->getReflectionProperty('a');
    $this->assertInstanceOf('PPHP\tools\patterns\metadata\reflection\ReflectionProperty', $rp);
    $this->assertEquals('a', $rp->getName());
  }

  /**
   * При повторном вызове должен возвращать ранее возращенное отражение.
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getReflectionProperty
   */
  public function testShouldReturnUniqueReflectionProperty(){
    $rp = self::$parent->getReflectionProperty('a');
    $this->assertEquals($rp, self::$parent->getReflectionProperty('a'));
  }

  /**
   * Должен возвращать отражение свойства родительского класса, если целевое свойство относится к нему.
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getReflectionProperty
   */
  public function testShouldReturnParentProperty(){
    /**
     * @var \PPHP\tools\patterns\metadata\reflection\ReflectionProperty $rp
     */
    $rp = self::$child->getReflectionProperty('a');
    $this->assertInstanceOf('PPHP\tools\patterns\metadata\reflection\ReflectionProperty', $rp);
    $this->assertEquals('PPHP\tests\tools\patterns\metadata\reflection\ParentReflectMock', $rp->getDeclaringClass()->getName());
  }

  /**
   * Должен выбрасывать исключение, если запрашиваемое свойство отсутствует в классе и в его родителях.
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getReflectionProperty
   */
  public function testShouldThrowExceptionIfPropertyNotExists(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\ComponentClassException');
    self::$child->getReflectionProperty('notProperty');
  }

  /**
   * Должен возвращать отражение метода класса.
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getReflectionMethod
   */
  public function testShouldReturnReflectionMethod(){
    /**
     * @var \PPHP\tools\patterns\metadata\reflection\ReflectionMethod $rm
     */
    $rm = self::$parent->getReflectionMethod('c');
    $this->assertInstanceOf('PPHP\tools\patterns\metadata\reflection\ReflectionMethod', $rm);
    $this->assertEquals('c', $rm->getName());
  }

  /**
   * При повторном вызове должен возвращать ранее возращенное отражение.
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getReflectionMethod
   */
  public function testShouldReturnUniqueReflectionMethod(){
    $rm = self::$parent->getReflectionMethod('c');
    $this->assertEquals($rm, self::$parent->getReflectionMethod('c'));
  }

  /**
   * Должен возвращать отражение метода родительского класса, если целевой метод относится к нему.
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getReflectionMethod
   */
  public function testShouldReturnParentMethod(){
    /**
     * @var \PPHP\tools\patterns\metadata\reflection\ReflectionMethod $rm
     */
    $rm = self::$child->getReflectionMethod('c');
    $this->assertInstanceOf('PPHP\tools\patterns\metadata\reflection\ReflectionMethod', $rm);
    $this->assertEquals('PPHP\tests\tools\patterns\metadata\reflection\ParentReflectMock', $rm->getDeclaringClass()->getName());
  }

  /**
   * Должен выбрасывать исключение, если запрашиваемый метод отсутствует в классе и в его родителях.
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getReflectionMethod
   */
  public function testShouldThrowExceptionIfMethodNotExists(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\ComponentClassException');
    self::$child->getReflectionMethod('notMethod');
  }

  /**
   * Должен возвращать отражения всех свойств, в том числе родительского класса.
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getAllReflectionProperties
   */
  public function testShouldReturnAllReflectionProperties(){
    /**
     * @var \PPHP\tools\patterns\metadata\reflection\ReflectionProperty[] $ps
     */
    $ps = self::$child->getAllReflectionProperties();
    $this->assertEquals('e', $ps['e']->getName());
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\reflection\ReflectionProperty', $ps['e']);
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\Described', $ps['e']);
    $this->assertEquals('a', $ps['a']->getName());
  }

  /**
   * Должен возвращать отражения всех методов, в том числе родительского класса.
   * @covers PPHP\tools\patterns\metadata\reflection\TReflect::getAllReflectionMethods
   */
  public function testShouldReturnAllReflectionMethods(){
    /**
     * @var \PPHP\tools\patterns\metadata\reflection\ReflectionMethod[] $ms
     */
    $ms = self::$child->getAllReflectionMethods();
    $this->assertEquals('h', $ms['h']->getName());
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\reflection\ReflectionMethod', $ms['h']);
    $this->assertInstanceOf('\PPHP\tools\patterns\metadata\Described', $ms['h']);
    $this->assertEquals('c', $ms['c']->getName());
  }
}
