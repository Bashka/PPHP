<?php
namespace PPHP\tests\tools\classes\standard\baseType;
use \PPHP\tools\classes\standard\baseType\String;
use \PPHP\tools\classes\standard\baseType\exceptions as exceptions;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class StringTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var String
   */
  protected $object;

  /**
   * Строка для теста.
   */
  const testString = 'Test string тестовая строка +/\\#`'; // 33 символа

  protected function setUp(){
    $this->object = new String(self::testString);
  }

  /**
   * @covers String::__construct
   */
  public function test__construct(){
    new String('');
    new String('1');
    new String('Hello');

    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new String(1);
  }

  /**
   * @covers String::reestablish
   */
  public function testReestablish(){
    $o = String::reestablish('1.0');
    $this->assertEquals('1.0', $o->getVal());

    $o = String::reestablish('');
    $this->assertEquals('', $o->getVal());

    $o = String::reestablish('Hello');
    $this->assertEquals('Hello', $o->getVal());
  }

  /**
   * @covers String::isReestablish
   */
  public function testisReestablish(){
    $this->assertTrue(String::isReestablish(''));
    $this->assertTrue(String::isReestablish('1'));
    $this->assertTrue(String::isReestablish('Hello'));
  }

  /**
   * @covers String::offsetExists
   */
  public function testOffsetExists(){
    $this->assertTrue($this->object->offsetExists(0));
    $this->assertTrue($this->object->offsetExists(5));
    $this->assertFalse($this->object->offsetExists(-1));
    $this->assertFalse($this->object->offsetExists(34));
  }

  /**
   * @covers String::offsetGet
   */
  public function testOffsetGet(){
    $this->assertEquals('T', $this->object[0]);
    $this->assertEquals('т', $this->object[12]);
  }

  /**
   * @covers String::offsetSet
   */
  public function testOffsetSet(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\RuntimeException');
    $this->object[0] = 'x';
  }

  /**
   * @covers String::offsetUnset
   */
  public function testOffsetUnset(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\RuntimeException');
    unset($this->object[0]);
  }

  /**
   * @covers String::current
   * @covers String::next
   * @covers String::prev
   * @covers String::key
   * @covers String::valid
   * @covers String::rewind
   *
   */
  public function testIterator(){
    $i = 0;
    $key = '';
    $val = '';
    foreach($this->object as $k => $v){
      $this->assertEquals($i, $k);
      $this->assertEquals(iconv_substr(self::testString, $i, 1, 'UTF-8'), $v);
      $key .= $k;
      $val .= $v;
      $i++;
    }

    $this->assertEquals('01234567891011121314151617181920212223242526272829303132', $key);
    $this->assertEquals('Test string тестовая строка +/\\#`', $val);
    $this->assertEquals(33, $i);
  }

  /**
   * @covers String::length
   */
  public function testLength(){
    $this->assertEquals(47, $this->object->length());
  }

  /**
   * @covers String::count
   */
  public function testCount(){
    $this->assertEquals(33, $this->object->count());
  }

  /**
   * @covers String::sub
   */
  public function testSub(){
    $this->assertEquals('est', $this->object->sub(1,3)->getVal());
    $this->assertEquals('string тестовая строка +/\\#`', $this->object->sub(5)->getVal());
    $this->assertEquals('string те', $this->object->sub(5,9)->getVal());
    $this->object->setPoint(2);
    $this->assertEquals('st', $this->object->sub(null,2)->getVal());
    $this->assertEquals('Te', $this->object->sub(null,-3)->getVal());
  }

  /**
   * @covers String::subLeft
   */
  public function testSubLeft(){
    $this->assertEquals('Test ', $this->object->subLeft(5)->getVal());
  }

  /**
   * @covers String::subRight
   */
  public function testSubRight(){
    $this->assertEquals('трока +/\\#`', $this->object->subRight(10)->getVal());
  }

  /**
   * @covers String::pad
   */
  public function testPad(){
    $this->assertEquals('  Test string тестовая строка +/\\#`', $this->object->pad(35)->getVal());
    $this->assertEquals('Test string тестовая строка +/\\#`  ', $this->object->pad(35, ' ', String::PAD_RIGHT)->getVal());
    $this->assertEquals(' Test string тестовая строка +/\\#` ', $this->object->pad(35, ' ', String::PAD_BOTH)->getVal());
    $this->assertEquals('_Test string тестовая строка +/\\#`_', $this->object->pad(35, '_', String::PAD_BOTH)->getVal());
  }

  /**
   * @covers String::replace
   */
  public function testReplace(){
    $this->assertEquals('Replace string тестовая строка +/\\#`', $this->object->replace('Test', 'Replace')->getVal());
    $this->assertEquals('Test string замененная строка +/\\#`', $this->object->replace('тестовая', 'замененная')->getVal());
  }

  /**
   * @covers String::change
   */
  public function testChange(){
    $this->assertEquals('Test st+ing +ес+овая с+рока +/\\+`', $this->object->change('/[rт#]+/u', '+')->getVal());
  }

  /**
   * @covers String::search
   */
  public function testSearch(){
    $this->assertEquals(5, $this->object->search('string тестовая'));
  }

  /**
   * @covers String::match
   */
  public function testMatch(){
    $this->assertEquals(1, $this->object->match('/.*string.*/u'));
    $this->assertEquals(0, $this->object->match('/.*modify.*/u'));
  }

  /**
   * @covers String::explode
   */
  public function testExplode(){
    $this->assertEquals(['Test','string','тестовая','строка','+/\\#`'], $this->object->explode(' '));
  }

  /**
   * @covers String::md5
   */
  public function testMd5(){
    $this->assertEquals('6b5948186119279f14452b5a7d7ba715', $this->object->md5());
  }

  /**
   * @covers String::sha1
   */
  public function testSha1(){
    $this->assertEquals('b10132ca6af9cff5579d609eb1e676bcf7d6ec3f', $this->object->sha1());
  }

  /**
   * @covers String::verify
   */
  public function testVerify(){
    $this->assertTrue($this->object->verify('== 33'));
    $this->assertTrue($this->object->verify('!= 32'));
    $this->assertTrue($this->object->verify('eq '.self::testString));
    $this->assertTrue($this->object->verify('!eq test'));
    $this->assertTrue($this->object->verify('# a-zA-Zа-яА-ЯёЁ+/\#` '));
    $this->assertTrue($this->object->verify('> 32'));
    $this->assertTrue($this->object->verify('>= 33'));
    $this->assertTrue($this->object->verify('< 34'));
    $this->assertTrue($this->object->verify('<= 33'));
    $this->assertTrue($this->object->verify('[] 0 33'));
    $this->assertTrue($this->object->verify('() 0 34'));

    $this->assertFalse($this->object->verify('== 32'));
    $this->assertFalse($this->object->verify('!= 33'));
    $this->assertFalse($this->object->verify('eq test'));
    $this->assertFalse($this->object->verify('!eq '.self::testString));
    $this->assertFalse($this->object->verify('# A-Zа-яА-ЯёЁ+/\#` '));
    $this->assertFalse($this->object->verify('> 34'));
    $this->assertFalse($this->object->verify('>= 34'));
    $this->assertFalse($this->object->verify('< 32'));
    $this->assertFalse($this->object->verify('<= 32'));
    $this->assertFalse($this->object->verify('[] 0 32'));
    $this->assertFalse($this->object->verify('() 0 33'));
  }

  /**
   * @covers String::prevent
   */
  public function testPrevent(){
    $this->assertEquals('Test string тестовая строка +/\\#`', $this->object->prevent(0,33, '')->getVal());
    $this->assertEquals('  Test string тестовая строка +/\\#`', $this->object->prevent(35,50, '')->getVal());
    $this->assertEquals('Test string тестовая строка +/', $this->object->prevent(0,30, '')->getVal());
    $this->assertEquals('Tes sring тестовая строка +/`', $this->object->prevent(0,33, '\t#')->getVal());
    $this->assertEquals('Test string тестовая строка +', $this->object->prevent(0,30, '\/')->getVal());
  }

  /**
   * @covers String::getPoint
   */
  public function testGetPoint(){
    $this->assertEquals(0, $this->object->getPoint());
  }

  /**
   * @covers String::setPoint
   * @covers String::getPoint
   */
  public function testSetPoint(){
    $this->object->setPoint(5);
    $this->assertEquals(5, $this->object->getPoint());
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    $this->object->setPoint(48);
    $this->object->setPoint(-1);
  }

  /**
   * @covers String::nextComponent
   */
  public function testNextComponent(){
    $this->assertEquals('Test', $this->object->nextComponent(' ')->getVal());
    $this->assertEquals('string', $this->object->nextComponent(' ')->getVal());
    $this->assertEquals('тест', $this->object->nextComponent('о')->getVal());
    $this->assertEquals('вая строка ', $this->object->nextComponent('+')->getVal());
  }
}
