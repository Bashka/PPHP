<?php
namespace PPHP\tests\tools\classes\standard\network\protocols\applied\http;

use PPHP\tools\classes\standard\network\protocols\applied\http\Header;
use PPHP\tools\classes\standard\network\protocols\applied\http\Message;
use PPHP\tools\classes\standard\network\protocols\applied\http\Parameter;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')).'PPHP/dev/autoload/autoload.php';

class MessageTest extends \PHPUnit_Framework_TestCase {
  /**
   * @var Message
   */
  protected $object;

  protected function setUp(){
    $this->object = new MessageMock();
  }

  /**
   * Может устанавливать заголовок и тело сообщения.
   * @covers PPHP\tools\classes\standard\network\protocols\applied\http\Message::__construct
   */
  public function testCanSetHeadAndBody(){
    $h = new Header();
    $m = new MessageMock($h, 'body');
    $this->assertEquals($h, $m->getHeader());
    $this->assertEquals('body', $m->getBody());
  }

  /**
   * Должен добавлять параметры Cache-Control и Connection автоматически в заголовок сообщения.
   * @covers PPHP\tools\classes\standard\network\protocols\applied\http\Message::__construct
   */
  public function testShouldAddDefaultParameters(){
    $this->assertEquals('no-cache', $this->object->getHeader()->getParameter('Cache-Control')->getValue());
    $this->assertEquals('close', $this->object->getHeader()->getParameter('Connection')->getValue());
  }

  /**
   * Должен устанавливать заголовок сообщения.
   * @covers PPHP\tools\classes\standard\network\protocols\applied\http\Message::setHeader
   */
  public function testShouldSetHead(){
    $h = new Header();
    $this->object->setHeader($h);
    $this->assertEquals($h, $this->object->getHeader());
  }

  /**
   * Должен возвращать заголовок сообщения.
   * @covers PPHP\tools\classes\standard\network\protocols\applied\http\Message::getHeader
   */
  public function testShouldReturnHead(){
    $h = new Header();
    $this->object->setHeader($h);
    $this->assertEquals($h, $this->object->getHeader());
  }

  /**
   * Должен добавлять параметр в заголовок сообщения.
   * @covers PPHP\tools\classes\standard\network\protocols\applied\http\Message::addParameterHeader
   */
  public function testShouldAddParamenter(){
    $this->object->setHeader(new Header());
    $p = new Parameter('name', 'value');
    $this->object->addParameterHeader($p);
    $p = $this->object->getHeader()->getParameter('name');
    $this->assertInstanceOf('PPHP\tools\classes\standard\network\protocols\applied\http\Parameter', $p);
    $this->assertEquals('value', $p->getValue());
  }

  /**
   * Должен создавать и добавлять параметр в заголовок сообщения из строки.
   * @covers PPHP\tools\classes\standard\network\protocols\applied\http\Message::addParameterHeaderStr
   */
  public function testShouldCreateAndAddParameter(){
    $this->object->setHeader(new Header());
    $this->object->addParameterHeaderStr('name', 'value');
    $p = $this->object->getHeader()->getParameter('name');
    $this->assertInstanceOf('PPHP\tools\classes\standard\network\protocols\applied\http\Parameter', $p);
    $this->assertEquals('value', $p->getValue());
  }

  /**
   * Должен добавлять тело сообщения.
   * @covers PPHP\tools\classes\standard\network\protocols\applied\http\Message::setBody
   */
  public function testShouldSetBody(){
    $this->object->setBody('body');
    $this->assertEquals('body', $this->object->getBody());
  }

  /**
   * Должен добавлять параметры Content-Type (если отсутствует), Content-Length и Content-MD5 автоматически в заголовок сообщения.
   * @covers PPHP\tools\classes\standard\network\protocols\applied\http\Message::setBody
   */
  public function testShouldAddDefaultParameters2(){
    $this->object->setBody('body');
    $this->assertEquals(4, $this->object->getHeader()->getParameter('Content-Length')->getValue());
    $this->assertEquals(md5('body'), $this->object->getHeader()->getParameter('Content-MD5')->getValue());
    $this->assertEquals('application/x-www-form-urlencoded;charset=utf-8', $this->object->getHeader()->getParameter('Content-Type')->getValue());
  }

  /**
   * Должен возвращать тело сообщения.
   * @covers PPHP\tools\classes\standard\network\protocols\applied\http\Message::getBody
   */
  public function testShouldReturnBody(){
    $this->object->setBody('body');
    $this->assertEquals('body', $this->object->getBody());
  }
}
