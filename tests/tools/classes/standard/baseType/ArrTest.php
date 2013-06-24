<?php
namespace PPHP\tests\tools\classes\standard\baseType;

use PPHP\tools\classes\standard\baseType\Arr;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class ArrTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var Arr
   */
  protected $object;

  protected function setUp(){
    $this->object = new Arr([false, 2, 3]);
  }

  /**
   * @covers Arr::__construct
   */
  public function test__construct(){
    new Arr([]);
    new Arr([1, 2, 3]);
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new Arr('true');
  }

  /**
   * @covers Arr::reestablish
   */
  public function testReestablish(){
    $o = Arr::reestablish('a');
    $this->assertEquals(['a'], $o->getVal());
  }

  /**
   * @covers Arr::offsetExists
   */
  public function testOffsetExists(){
    $this->assertTrue($this->object->offsetExists(0));
    $this->assertTrue($this->object->offsetExists(2));
    $this->assertFalse($this->object->offsetExists(-1));
    $this->assertFalse($this->object->offsetExists(3));
  }

  /**
   * @covers Arr::offsetGet
   */
  public function testOffsetGet(){
    $this->assertEquals(false, $this->object[0]);
    $this->assertEquals(3, $this->object[2]);
  }

  /**
   * @covers Arr::offsetSet
   */
  public function testOffsetSet(){
    $this->object[0] = 'x';
    $this->assertEquals('x', $this->object[0]);
  }

  /**Arr
   * @covers Arr::current
   * @covers Arr::next
   * @covers Arr::prev
   * @covers Arr::key
   * @covers Arr::valid
   * @covers Arr::rewind
   */
  public function testIterator(){
    $i = 0;
    $key = '';
    $val = '';
    foreach($this->object as $k => $v){
      $this->assertEquals($i, $k);
      $key .= (string) $k;
      $val .= (string) $v;
      $i++;
    }
    $this->assertEquals('012', $key);
    $this->assertEquals('23', $val);
    $this->assertEquals(3, $i);
  }

  /**
   * @covers Arr:count
   */
  public function testCount(){
    $this->assertEquals(3, $this->object->count());
  }

  /**
   * @covers Arr:shift
   */
  public function testShift(){
    $this->assertEquals(false, $this->object->shift());
    $this->assertEquals(2, $this->object->count());
  }

  /**
   * @covers Arr:unshift
   */
  public function testUnshift(){
    $this->object->unshift(0);
    $this->assertEquals(4, $this->object->count());
    $this->assertEquals(0, $this->object->shift());
  }

  /**
   * @covers Arr:pop
   */
  public function testPop(){
    $this->assertEquals(3, $this->object->pop());
    $this->assertEquals(2, $this->object->count());
  }

  /**
   * @covers Arr:push
   */
  public function testPush(){
    $this->object->push(4);
    $this->assertEquals(4, $this->object->count());
    $this->assertEquals(4, $this->object->pop());
  }

  /**
   * @covers Arr:hasKey
   */
  public function testHasKey(){
    $this->assertTrue($this->object->hasKey(1));
    $this->assertFalse($this->object->hasKey(3));
  }

  /**
   * @covers Arr:searchVal
   */
  public function testSearchVal(){
    $this->assertEquals([1], $this->object->searchVal(2)->getVal());
    $this->assertEquals([0, 1], (new Arr([1, 1, 2]))->searchVal(1)->getVal());
  }

  /**
   * @covers Arr:slice
   */
  public function testSlice(){
    $this->assertEquals([false, 2], $this->object->slice(0, 2)->getVal());
    $this->assertEquals([false, 2, 3], $this->object->slice(0)->getVal());
    $this->assertEquals([1 => 2, 2 => 3], $this->object->slice(-2)->getVal());
  }

  /**
   * @covers Arr:splice
   */
  public function testSplice(){
    $this->assertEquals([false, 2], $this->object->splice(0, 2)->getVal());
    $this->assertEquals(1, $this->object->count());
    $this->object = new Arr([false, 2, 3]);
    $this->assertEquals([false, 2, 3], $this->object->splice(0)->getVal());
    $this->assertEquals(0, $this->object->count());
    $this->object = new Arr([false, 2, 3]);
    $this->assertEquals([2, 3], $this->object->splice(-2)->getVal());
    $this->assertEquals(1, $this->object->count());
  }

  /**
   * @covers Arr:verify
   */
  public function testVerify(){
    $this->assertTrue($this->object->verify('== 3'));
    $this->assertTrue($this->object->verify('!= 4'));
    $this->assertTrue($this->object->verify('> 2'));
    $this->assertTrue($this->object->verify('>= 3'));
    $this->assertTrue($this->object->verify('< 4'));
    $this->assertTrue($this->object->verify('<= 3'));
    $this->assertTrue($this->object->verify('[] 0 3'));
    $this->assertTrue($this->object->verify('() 0 4'));
    $this->assertFalse($this->object->verify('== 2'));
    $this->assertFalse($this->object->verify('!= 3'));
    $this->assertFalse($this->object->verify('> 3'));
    $this->assertFalse($this->object->verify('>= 4'));
    $this->assertFalse($this->object->verify('< 3'));
    $this->assertFalse($this->object->verify('<= 2'));
    $this->assertFalse($this->object->verify('[] 0 2'));
    $this->assertFalse($this->object->verify('() 0 3'));
  }
}
