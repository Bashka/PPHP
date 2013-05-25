<?php
namespace PPHP\tests\tools\classes\standard\baseType;
use \PPHP\tools\classes\standard\baseType\Float;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class FloatTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var Float
   */
  protected $object;

  protected function setUp(){
    $this->object = new Float(1.2345);
  }

  /**
   * @covers Float::__construct
   */
  public function test__construct(){
    new Float(0.0);
    new Float(-1.1);
    new Float(1.0);

    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new Float(1);
  }

  /**
   * @covers Float::reestablish
   */
  public function testReestablish(){
    $o = Float::reestablish('1.0');
    $this->assertEquals(1.0, $o->getVal());

    $o = Float::reestablish('-1.1');
    $this->assertEquals(-1.1, $o->getVal());

    $o = Float::reestablish('0.0');
    $this->assertEquals(0, $o->getVal());
  }

  /**
   * @covers Float::isReestablish
   */
  public function testisReestablish(){
    $this->assertTrue(Float::isReestablish('1.0'));
    $this->assertTrue(Float::isReestablish('-1.1'));
    $this->assertTrue(Float::isReestablish('11.1'));
    $this->assertTrue(Float::isReestablish('10.0'));
    $this->assertTrue(Float::isReestablish('-10.0'));
    $this->assertTrue(Float::isReestablish('1.5'));
    $this->assertTrue(Float::isReestablish('1.15'));
    $this->assertTrue(Float::isReestablish('0.0'));

    $this->assertFalse(Float::isReestablish(''));
    $this->assertFalse(Float::isReestablish('t'));
    $this->assertFalse(Float::isReestablish('1'));
  }

  /**
   * @covers Float::verify
   */
  public function testVerify(){
    $this->assertTrue($this->object->verify('== 1.2345'));
    $this->assertTrue($this->object->verify('!= 1.5'));
    $this->assertTrue($this->object->verify('> 1.1'));
    $this->assertTrue($this->object->verify('>= 1.12345'));
    $this->assertTrue($this->object->verify('< 1.5'));
    $this->assertTrue($this->object->verify('<= 1.2345'));
    $this->assertTrue($this->object->verify('[] 1.1 1.3'));
    $this->assertTrue($this->object->verify('[] 1.2345 1.3'));
    $this->assertTrue($this->object->verify('() 1.1 1.3'));
    $this->assertTrue($this->object->verify('in 12344 1.2345 12346'));
    $this->assertTrue($this->object->verify('!in 12344 12346'));

    $this->assertFalse($this->object->verify('== 1.1'));
    $this->assertFalse($this->object->verify('!= 1.2345'));
    $this->assertFalse($this->object->verify('> 1.3'));
    $this->assertFalse($this->object->verify('>= 1.3'));
    $this->assertFalse($this->object->verify('< 1.1'));
    $this->assertFalse($this->object->verify('<= 1.1'));
    $this->assertFalse($this->object->verify('[] 1.1 1.2'));
    $this->assertFalse($this->object->verify('() 1.2345 2'));
    $this->assertFalse($this->object->verify('in 1.1 1.2'));
    $this->assertFalse($this->object->verify('!in 1.1 1.2345 1.3'));
  }

  /**
   * @covers Float::prevent
   */
  public function testPrevent(){
    $this->assertEquals(1.2345, $this->object->prevent(0.0, 2.0)->getVal());
    $this->assertEquals(2.0, $this->object->prevent(2.0)->getVal());
    $this->assertEquals(1.0, $this->object->prevent(null, 1.0)->getVal());
  }
}
