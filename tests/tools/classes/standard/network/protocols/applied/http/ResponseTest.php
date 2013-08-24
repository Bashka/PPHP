<?php
namespace PPHP\tests\tools\classes\standard\network\protocols\applied\http;

use \PPHP\tools\classes\standard\network\protocols\applied\http as http;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class ResponseTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var http\Response
   */
  protected $object;

  protected function setUp(){
    $this->object = new http\Response(200, 'OK');
    $this->object->addParameterHeaderStr('nameA', 'valueA');
    $this->object->addParameterHeaderStr('nameB', 'valueB');
    $this->object->setBody('test body');
  }

  /**
   * @covers http\Response::reestablish
   */
  public function testReestablish(){
    $request = http\Response::reestablish('HTTP/1.1 200 OK test' . "\r\n" . 'Content-Type:text/html' . "\r\n" . 'Content-Length:2' . "\r\n" . "\r\n" . 'test');
    $this->assertEquals('200', $request->getCode());
    $this->assertEquals('OK test', $request->getMessage());
    $this->assertEquals('text/html', $request->getHeader()->getParameterValue('Content-Type'));
    $this->assertEquals('te', $request->getBody());
    $request = http\Response::reestablish('HTTP/1.1 200 OK' . "\r\n" . "\r\n");
    $this->assertEquals('200', $request->getCode());
    $this->assertEquals('OK', $request->getMessage());
    $this->assertEquals('Cache-Control:no-cache' . "\r\n" . 'Connection:close' . "\r\n", $request->getHeader()->interpretation());
    $this->assertEquals(null, $request->getBody());
  }

  /**
   * @covers http\Response::isReestablish
   */
  public function testIsReestablish(){
    $this->assertTrue(http\Response::isReestablish('HTTP/1.1 200 OK' . "\r\n" . "\r\n"));
    $this->assertTrue(http\Response::isReestablish('HTTP/1.1 0 X' . "\r\n" . "\r\n"));
    $this->assertTrue(http\Response::isReestablish('HTTP/1.1 999 X' . "\r\n" . "\r\n"));
    $this->assertTrue(http\Response::isReestablish('HTTP/1.1 200 OK test' . "\r\n" . "\r\n"));
    $this->assertTrue(http\Response::isReestablish('HTTP/1.1 200 OK' . "\r\n" . 'Cache-Control:no-cache' . "\r\n" . "\r\n"));
    $this->assertTrue(http\Response::isReestablish('HTTP/1.1 200 OK' . "\r\n" . 'Cache-Control:no-cache' . "\r\n" . 'Connection:close' . "\r\n" . "\r\n"));
    $this->assertTrue(http\Response::isReestablish('HTTP/1.1 200 OK' . "\r\n" . 'Cache-Control:no-cache' . "\r\n" . 'Connection:close' . "\r\n" . "\r\n" . 'Body'));
    $this->assertFalse(http\Response::isReestablish(''));
    $this->assertFalse(http\Response::isReestablish('200 OK' . "\r\n" . "\r\n"));
    $this->assertFalse(http\Response::isReestablish('HTTP/1.1  OK' . "\r\n" . "\r\n"));
    $this->assertFalse(http\Response::isReestablish('HTTP/1.1 200 ' . "\r\n" . "\r\n"));
    $this->assertFalse(http\Response::isReestablish('HTTP/1.1 200 OK' . "\r\n"));
    $this->assertFalse(http\Response::isReestablish('HTTP/1.1 200 OK'));
    $this->assertFalse(http\Response::isReestablish('HTTP/1.1 200 OK' . "\r\n" . 'Cache-Control' . "\r\n" . "\r\n"));
    $this->assertFalse(http\Response::isReestablish('HTTP/1.1 200 OK' . "\r\n" . 'Cache-Control:no-cache' . "\r\n"));
  }

  /**
   * @covers http\Response::interpretation
   */
  public function testInterpretation(){
    $this->assertEquals('HTTP/1.1 200 OK' . "\r\n" . 'Cache-Control:no-cache' . "\r\n" . 'Connection:close' . "\r\n" . 'nameA:valueA' . "\r\n" . 'nameB:valueB' . "\r\n" . 'Content-Type:application/x-www-form-urlencoded;charset=utf-8' . "\r\n" . 'Content-Length:9' . "\r\n" . 'Content-MD5:bbf9afe7431caf5f89a608bc31e8d822' . "\r\n" . "\r\n" . 'test body', $this->object->interpretation());
    $response = new http\Response(200, 'OK', null, ['nameA' => 'valueA test', 'nameB' => 'valueB']);
    $this->assertEquals('HTTP/1.1 200 OK' . "\r\n" . 'Cache-Control:no-cache' . "\r\n" . 'Connection:close' . "\r\n" . 'Content-Type:application/x-www-form-urlencoded;charset=utf-8' . "\r\n" . 'Content-Length:30' . "\r\n" . 'Content-MD5:8c8f23f26ce46952da4e12cf85d66742' . "\r\n" . "\r\n" . 'nameA=valueA+test&nameB=valueB', $response->interpretation());
  }

  /**
   * @covers http\Response::getCode
   */
  public function testGetCode(){
    $this->assertEquals('200', $this->object->getCode());
  }

  /**
   * @covers http\Response::getMessage
   */
  public function testGetMessage(){
    $this->assertEquals('OK', $this->object->getMessage());
  }
}
