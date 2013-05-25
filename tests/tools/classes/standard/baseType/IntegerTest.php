<?php
namespace PPHP\tests\tools\classes\standard\baseType;
use \PPHP\tools\classes\standard\baseType\Integer;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class IntegerTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var Integer
   */
  protected $object;

  protected function setUp(){
    $this->object = new Integer(12345);
  }

  /**
   * @covers Integer::__construct
   */
  public function test__construct(){
    new Integer(0);
    new Integer(-1);
    new Integer(1);

    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new Integer(1.0);
  }

  /**
   * @covers Integer::reestablish
   */
  public function testReestablish(){
    $o = Integer::reestablish('1');
    $this->assertEquals(1, $o->getVal());

    $o = Integer::reestablish('-1');
    $this->assertEquals(-1, $o->getVal());

    $o = Integer::reestablish('0');
    $this->assertEquals(0, $o->getVal());

    $o = Integer::reestablish('-1.0');
    $this->assertEquals(-1, $o->getVal());
  }

  /**
   * @covers Integer::isReestablish
   */
  public function testisReestablish(){
    $this->assertTrue(Integer::isReestablish('0'));
    $this->assertTrue(Integer::isReestablish('1'));
    $this->assertTrue(Integer::isReestablish('-1'));
    $this->assertTrue(Integer::isReestablish('11'));
    $this->assertTrue(Integer::isReestablish('10'));
    $this->assertTrue(Integer::isReestablish('-10'));
    $this->assertTrue(Integer::isReestablish('1.0'));
    $this->assertTrue(Integer::isReestablish('1.00'));
    $this->assertTrue(Integer::isReestablish('0.0'));

    $this->assertFalse(Integer::isReestablish(''));
    $this->assertFalse(Integer::isReestablish('t'));
    $this->assertFalse(Integer::isReestablish('1.1'));
  }

  /**
   * @covers Integer::isEven
   */
  public function testIsEven(){
    $this->assertFalse($this->object->isEven());
    $this->assertTrue((new Integer(2))->isEven());
  }

  /**
   * @covers Integer::count
   */
  public function testCount(){
    $this->assertEquals(5, $this->object->count());
    $negativeInt = new Integer(-12345);
    $this->assertEquals(5, $negativeInt->count());
  }

  /**
   * @covers Integer::verify
   */
  public function testVerify(){
    $this->assertTrue($this->object->verify('== 12345'));
    $this->assertTrue($this->object->verify('!= 1'));
    $this->assertTrue($this->object->verify('> 1'));
    $this->assertTrue($this->object->verify('>= 12345'));
    $this->assertTrue($this->object->verify('< 12346'));
    $this->assertTrue($this->object->verify('<= 12345'));
    $this->assertTrue($this->object->verify('[] 12344 12346'));
    $this->assertTrue($this->object->verify('[] 12345 12346'));
    $this->assertTrue($this->object->verify('() 12344 12346'));
    $this->assertTrue($this->object->verify('in 12344 12345 12346'));
    $this->assertTrue($this->object->verify('!in 12344 12346'));

    $this->assertFalse($this->object->verify('== 12346'));
    $this->assertFalse($this->object->verify('!= 12345'));
    $this->assertFalse($this->object->verify('> 12345'));
    $this->assertFalse($this->object->verify('>= 12346'));
    $this->assertFalse($this->object->verify('< 12345'));
    $this->assertFalse($this->object->verify('<= 12344'));
    $this->assertFalse($this->object->verify('[] 12343 12344'));
    $this->assertFalse($this->object->verify('() 12345 12346'));
    $this->assertFalse($this->object->verify('in 12344 12346'));
    $this->assertFalse($this->object->verify('!in 12344 12345 12346'));
  }

  /**
   * @covers Integer::prevent
   */
  public function testPrevent(){
    $this->assertEquals(12345, $this->object->prevent(0, 12345)->getVal());
    $this->assertEquals(12346, $this->object->prevent(12346)->getVal());
    $this->assertEquals(1000, $this->object->prevent(null, 1000)->getVal());
  }
}
