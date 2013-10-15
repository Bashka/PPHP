<?php
namespace PPHP\tests\tools\classes\standard\network\protocols\applied\http;

use PPHP\tools\classes\standard\network\protocols\applied\http\Request;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')).'PPHP/dev/autoload/autoload.php';

class RequestTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var Request
   */
  protected $object;

  protected function setUp(){
    $this->object = new Request('localhost', '/index.html');
    $this->object->addParameterHeaderStr('nameA', 'valueA');
    $this->object->addParameterHeaderStr('nameB', 'valueB');
    $this->object->setBody('test body');
  }

  /**
   * Должен добавлять параметр заголовка Host.
   * @covers PPHP\tools\classes\standard\network\protocols\applied\http\Request::__construct
   */
  public function testShouldHostParameter(){
    $r = new Request('localhost', '');
    $this->assertEquals('localhost', $r->getHeader()->getParameterValue('Host'));
  }

  /**
   * Должен добавлять тело запроса в URI, если метод GET.
   * @covers PPHP\tools\classes\standard\network\protocols\applied\http\Request::__construct
   */
  public function testShouldAddBodyInURIIfMethodGet(){
    $r = new Request('localhost', '', Request::GET, null, ['a' => 1, 'b' => 2]);
    $this->assertEquals('/?a=1&b=2', $r->getURI());
  }

  /**
   * Должен возвращать строку вида: основнойЗаголовок<разделитель>заголовок<разделитель><разделитель>тело
   * @covers PPHP\tools\classes\standard\network\protocols\applied\http\Request::interpretation
   */
  public function testShouldInterpretation(){
    $this->assertEquals('GET /index.html HTTP/1.1' . "\r\n" . 'Cache-Control:no-cache' . "\r\n" . 'Connection:close' . "\r\n" . 'Host:localhost' . "\r\n" . 'nameA:valueA' . "\r\n" . 'nameB:valueB' . "\r\n" . 'Content-Type:application/x-www-form-urlencoded;charset=utf-8' . "\r\n" . 'Content-Length:9' . "\r\n" . 'Content-MD5:bbf9afe7431caf5f89a608bc31e8d822' . "\r\n" . "\r\n" . 'test body', $this->object->interpretation());

    $request = new Request('localhost', '/index.html', 'GET', null, ['nameA' => 'valueA test', 'nameB' => 'valueB']);
    $this->assertEquals('GET /index.html?nameA=valueA+test&nameB=valueB HTTP/1.1' . "\r\n" . 'Cache-Control:no-cache' . "\r\n" . 'Connection:close' . "\r\n" . 'Host:localhost' . "\r\n" . "\r\n", $request->interpretation());
  }

  /**
   * Может быть восстановлен из сроки вида: основнойЗаголовок<разделитель>заголовок<разделитель><разделитель>тело
   * @covers PPHP\tools\classes\standard\network\protocols\applied\http\Request::reestablish
   */
  public function testShouldRestorableForString(){
    $request = Request::reestablish('GET /index.html HTTP/1.1' . "\r\n" . 'Content-Type:text/html' . "\r\n" . 'Content-Length:4' . "\r\n" . 'Host:localhost' . "\r\n" . "\r\n" . 'Body');
    $this->assertEquals('Body', $request->getBody());
    $this->assertEquals('GET', $request->getMethod());
    $this->assertEquals('/index.html', $request->getURI());
    $this->assertEquals('text/html', $request->getHeader()->getParameterValue('Content-Type'));

    $request = Request::reestablish('GET /index.html HTTP/1.1' . "\r\n" . 'Host:localhost' . "\r\n" . "\r\n");
    $this->assertEquals('GET', $request->getMethod());
    $this->assertEquals('/index.html', $request->getURI());
    $this->assertEquals('localhost', $request->getHeader()->getParameterValue('Host'));

    $request = Request::reestablish('GET /index.html?name=value HTTP/1.1' . "\r\n" . 'Host:localhost' . "\r\n" . "\r\n");
    $this->assertEquals('/index.html?name=value', $request->getURI());

    $request = Request::reestablish('POST / HTTP/1.1' . "\r\n" . 'Host:localhost' . "\r\n" . "\r\n");
    $this->assertEquals('POST', $request->getMethod());
    $this->assertEquals('/', $request->getURI());
    $this->assertEquals(null, $request->getBody());

    $request = Request::reestablish('POST /index.html HTTP/1.1' . "\r\n" . 'Content-Type:text/html' . "\r\n" . 'Content-Length:8' . "\r\n" . 'Host:localhost' . "\r\n" . "\r\n" . 'тест');
    $this->assertEquals('тест', $request->getBody());
  }

  /**
   * Допустимой строкой является строка вида: основнойЗаголовок<разделитель>заголовок<разделитель><разделитель>тело
   * @covers PPHP\tools\classes\standard\network\protocols\applied\http\Request::isReestablish
   */
  public function testGoodString(){
    $this->assertTrue(Request::isReestablish('GET index.php HTTP/1.1' . "\r\n" . "\r\n"));
    $this->assertTrue(Request::isReestablish('POST index.php HTTP/1.1' . "\r\n" . "\r\n"));
    $this->assertTrue(Request::isReestablish('GET / HTTP/1.1' . "\r\n" . "\r\n"));
    $this->assertTrue(Request::isReestablish('POST index.php?name=value HTTP/1.1' . "\r\n" . "\r\n"));
    $this->assertTrue(Request::isReestablish('POST index.php?a=b&c=d HTTP/1.1' . "\r\n" . "\r\n"));
    $this->assertTrue(Request::isReestablish('GET index.php HTTP/1.1' . "\r\n" . 'Cache-Control:no-cache' . "\r\n" . "\r\n"));
    $this->assertTrue(Request::isReestablish('GET index.php HTTP/1.1' . "\r\n" . 'Cache-Control:no-cache' . "\r\n" . 'Connection:close' . "\r\n" . "\r\n"));
    $this->assertTrue(Request::isReestablish('GET index.php HTTP/1.1' . "\r\n" . 'Cache-Control:no-cache' . "\r\n" . 'Connection:close' . "\r\n" . "\r\n" . 'Body'));
  }

  /**
   * Должен возвращать false при передаче строки недопустимой структуры.
   * @covers PPHP\tools\classes\standard\network\protocols\applied\http\Request::isReestablish
   */
  public function testBedString(){
    $this->assertFalse(Request::isReestablish(''));
    $this->assertFalse(Request::isReestablish('index.php HTTP/1.1' . "\r\n" . "\r\n"));
    $this->assertFalse(Request::isReestablish('GET HTTP/1.1' . "\r\n" . "\r\n"));
    $this->assertFalse(Request::isReestablish('GET index.php' . "\r\n" . "\r\n"));
    $this->assertFalse(Request::isReestablish('GET index.php HTTP/1.1'));
    $this->assertFalse(Request::isReestablish('GET index.php HTTP/1.1' . "\r\n"));
    $this->assertFalse(Request::isReestablish('GET index.php HTTP/1.1' . "\r\n" . 'no-cache' . "\r\n" . "\r\n"));
    $this->assertFalse(Request::isReestablish('GET index.php HTTP/1.1' . "\r\n" . 'Cache-Control:no-cache' . "\r\n"));
  }

  /**
   * Должен возвращать метод запроса.
   * @covers PPHP\tools\classes\standard\network\protocols\applied\http\Request::getMethod
   */
  public function testShouldReturnMethod(){
    $this->assertEquals(Request::GET, $this->object->getMethod());
  }

  /**
   * Должен возвращать URI запроса.
   * @covers PPHP\tools\classes\standard\network\protocols\applied\http\Request::getURI
   */
  public function testShouldReturnURI(){
    $this->assertEquals('/index.html', $this->object->getURI());
  }
}
