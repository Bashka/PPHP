<?php
namespace PPHP\tests\tools\classes\standard\baseType\special\network;

use PPHP\tools\classes\standard\baseType\special\network\URL;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class URLTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var URL
   */
  protected $object;

  protected function setUp(){
    $this->object = new URL('http://test.com:8080/testDir/testFile.txt');
  }

  /**
   * @covers URL::isReestablish
   */
  public function testIsReestablish(){
    $this->assertFalse(URL::isReestablish(''));
    $this->assertTrue(URL::isReestablish('http://test'));
    $this->assertTrue(URL::isReestablish('http://test.com'));
    $this->assertTrue(URL::isReestablish('http://test.com:8080'));
    $this->assertTrue(URL::isReestablish('http://test.com/test/text.txt'));
    $this->assertTrue(URL::isReestablish('http://test.com:8080/test/text.txt'));
    $this->assertFalse(URL::isReestablish('http://test.com//test/text.txt'));
    $this->assertFalse(URL::isReestablish('test://test.com:8080/test/text.txt'));
    $this->assertFalse(URL::isReestablish('http://test.com:8080//test/text.txt'));
    $this->assertFalse(URL::isReestablish('http://a:8080/test/text.txt'));
    $this->assertFalse(URL::isReestablish('http://a:/test/text.txt'));
    $this->assertTrue(URL::isReestablish('http://test.com:8080/test/'));
    $this->assertTrue(URL::isReestablish('http://test.com:8080/'));
    $this->assertTrue(URL::isReestablish('http://192.168.1.1'));
    $this->assertTrue(URL::isReestablish('http://192.168.1.1:8080/test/text.txt'));
  }

  /**
   * @covers URL::reestablish
   * @covers URL::getReport
   * @covers URL::getAddress
   * @covers URL::getPort
   * @covers URL::getFileSystemAddress
   */
  public function testReestablish(){
    $o = URL::reestablish('http://test.com:8080/test/text.txt');
    $this->assertEquals('http', $o->getReport()->getName());
    $this->assertEquals('com', $o->getAddress()->getComponent(0));
    $this->assertEquals('8080', $o->getPort()->getVal());
    $this->assertEquals('/test/text.txt', $o->getFileSystemAddress()->getVal());
    $this->assertTrue($o->getFileSystemAddress()->isRoot());
    $o = URL::reestablish('http://192.168.1.1:8080/test/text.txt');
    $this->assertEquals('168', $o->getAddress()->getTrio(1));
  }
}
