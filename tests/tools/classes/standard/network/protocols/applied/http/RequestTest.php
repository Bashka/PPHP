<?php
namespace PPHP\tests\tools\classes\standard\network\protocols\applied\http;
use \PPHP\tools\classes\standard\network\protocols\applied\http as http;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class RequestTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var http\Request
   */
  protected $object;

  protected function setUp(){
    $this->object = new http\Request('localhost', '/index.html');
    $this->object->addParameterHeaderStr('nameA', 'valueA');
    $this->object->addParameterHeaderStr('nameB', 'valueB');
    $this->object->setBody('test body');
  }

  /**
   * @covers http\Request::reestablish
   */
  public function testReestablish(){
    $request = http\Request::reestablish('GET /index.html HTTP/1.1' . "\r\n" . 'Content-Type:text/html' . "\r\n" . 'Content-Length:4' . "\r\n" . 'Host:localhost' . "\r\n" . "\r\n" . 'Body');
    $this->assertEquals('Body', $request->getBody());
    $this->assertEquals('GET', $request->getMethod());
    $this->assertEquals('/index.html', $request->getURI());
    $this->assertEquals('text/html', $request->getHeader()->getParameterValue('Content-Type'));

    $request = http\Request::reestablish('GET /index.html HTTP/1.1' . "\r\n"  . 'Host:localhost' . "\r\n" . "\r\n");
    $this->assertEquals('GET', $request->getMethod());
    $this->assertEquals('/index.html', $request->getURI());
    $this->assertEquals('localhost', $request->getHeader()->getParameterValue('Host'));

    $request = http\Request::reestablish('GET /index.html?name=value HTTP/1.1' . "\r\n"  . 'Host:localhost' . "\r\n" . "\r\n");
    $this->assertEquals('/index.html?name=value', $request->getURI());

    $request = http\Request::reestablish('POST / HTTP/1.1' . "\r\n"  . 'Host:localhost' . "\r\n" . "\r\n");
    $this->assertEquals('POST', $request->getMethod());
    $this->assertEquals('/', $request->getURI());
    $this->assertEquals(null, $request->getBody());

    $request = http\Request::reestablish('POST /index.html HTTP/1.1' . "\r\n" . 'Content-Type:text/html' . "\r\n" . 'Content-Length:8' . "\r\n" . 'Host:localhost' . "\r\n" . "\r\n" . 'тест');
    $this->assertEquals('тест', $request->getBody());
  }

  /**
   * @covers http\Request::isReestablish
   */
  public function testIsReestablish(){
    $this->assertTrue(http\Request::isReestablish('GET index.php HTTP/1.1'."\r\n"."\r\n"));
    $this->assertTrue(http\Request::isReestablish('POST index.php HTTP/1.1'."\r\n"."\r\n"));
    $this->assertTrue(http\Request::isReestablish('GET / HTTP/1.1'."\r\n"."\r\n"));

    $this->assertTrue(http\Request::isReestablish('POST index.php?name=value HTTP/1.1'."\r\n"."\r\n"));
    $this->assertTrue(http\Request::isReestablish('POST index.php?a=b&c=d HTTP/1.1'."\r\n"."\r\n"));

    $this->assertTrue(http\Request::isReestablish('GET index.php HTTP/1.1'."\r\n".'Cache-Control:no-cache'."\r\n"."\r\n"));
    $this->assertTrue(http\Request::isReestablish('GET index.php HTTP/1.1'."\r\n".'Cache-Control:no-cache'."\r\n".'Connection:close'."\r\n"."\r\n"));

    $this->assertTrue(http\Request::isReestablish('GET index.php HTTP/1.1'."\r\n".'Cache-Control:no-cache'."\r\n".'Connection:close'."\r\n"."\r\n".'Body'));

    $this->assertFalse(http\Request::isReestablish(''));
    $this->assertFalse(http\Request::isReestablish('index.php HTTP/1.1'."\r\n"."\r\n"));
    $this->assertFalse(http\Request::isReestablish('GET HTTP/1.1'."\r\n"."\r\n"));
    $this->assertFalse(http\Request::isReestablish('GET index.php'."\r\n"."\r\n"));
    $this->assertFalse(http\Request::isReestablish('GET index.php HTTP/1.1'));
    $this->assertFalse(http\Request::isReestablish('GET index.php HTTP/1.1'."\r\n"));
    $this->assertFalse(http\Request::isReestablish('GET index.php HTTP/1.1'."\r\n".'no-cache'."\r\n"."\r\n"));
    $this->assertFalse(http\Request::isReestablish('GET index.php HTTP/1.1'."\r\n".'Cache-Control:no-cache'."\r\n"));
  }

  /**
   * @covers http\Message::setHeader
   * @covers http\Message::getHeader
   */
  public function testSetHeader(){
    $header = http\Header::reestablish('nameA:valueA'."\r\n".'nameB:valueB'."\r\n");
    $this->object->setHeader($header);
    $this->assertEquals($header, $this->object->getHeader());
    $this->assertEquals('nameA:valueA'."\r\n".'nameB:valueB'."\r\n", $header->interpretation());
  }

  /**
   * @covers http\Message::addParameterHeader
   */
  public function testAddParameterHeader(){
    $this->object->addParameterHeader(new http\Parameter('nameC', 'valueC'));
    $this->assertEquals('valueC', $this->object->getHeader()->getParameterValue('nameC'));
  }

  /**
   * @covers http\Message::addParameterHeaderStr
   */
  public function testAddParameterHeaderStr(){
    $this->object->addParameterHeaderStr('nameC', 'valueC');
    $this->assertEquals('valueC', $this->object->getHeader()->getParameterValue('nameC'));
  }

  /**
   * @covers http\Message::setBody
   */
  public function testSetBody(){
    $this->object->setBody('new test');
    $this->assertEquals('new test', $this->object->getBody());
    $this->assertEquals('8', $this->object->getHeader()->getParameterValue('Content-Length'));

    $this->object->setBody('new test', 'text/json', 'windows-1251');
    $this->assertEquals('new test', $this->object->getBody());
    $this->assertEquals('8', $this->object->getHeader()->getParameterValue('Content-Length'));
    $this->assertEquals('application/x-www-form-urlencoded;charset=utf-8', $this->object->getHeader()->getParameterValue('Content-Type'));

    $this->object = new http\Request('localhost', '/index.html');
    $this->object->setBody('new test', 'text/json', 'windows-1251');
    $this->assertEquals('new test', $this->object->getBody());
    $this->assertEquals('8', $this->object->getHeader()->getParameterValue('Content-Length'));
    $this->assertEquals('text/json;charset=windows-1251', $this->object->getHeader()->getParameterValue('Content-Type'));

    $this->object->setBody('тест');
    $this->assertEquals('8', $this->object->getHeader()->getParameterValue('Content-Length'));
  }

  /**
   * @covers http\Request::interpretation
   */
  public function testInterpretation(){
    $this->assertEquals('GET /index.html HTTP/1.1'."\r\n".'Cache-Control:no-cache'."\r\n".'Connection:close'."\r\n".'Host:localhost'."\r\n".'nameA:valueA' . "\r\n" . 'nameB:valueB' . "\r\n".'Content-Type:application/x-www-form-urlencoded;charset=utf-8'."\r\n".'Content-Length:9'."\r\n".'Content-MD5:bbf9afe7431caf5f89a608bc31e8d822'."\r\n"."\r\n".'test body', $this->object->interpretation());
    $request = new http\Request('localhost', '/index.html', 'GET', null, ['nameA' => 'valueA test', 'nameB' => 'valueB']);
    $this->assertEquals('GET /index.html?nameA=valueA+test&nameB=valueB HTTP/1.1'."\r\n".'Cache-Control:no-cache'."\r\n".'Connection:close'."\r\n".'Host:localhost'."\r\n"."\r\n", $request->interpretation());
  }

  /**
   * @covers http\Message::getBody
   */
  public function testGetBody(){
    $this->assertEquals('test body', $this->object->getBody());
  }

  /**
   * @covers http\Request::getMethod
   */
  public function testGetMethod(){
    $this->assertEquals('GET', $this->object->getMethod());
  }

  /**
   * @covers http\Request::getURI
   */
  public function testGetURI(){
    $this->assertEquals('/index.html', $this->object->getURI());
  }
}
