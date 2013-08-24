<?php
namespace PPHP\tests\tools\classes\standard\network\socket;

use \PPHP\tools\classes\standard\network\socket as socket;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class StreamTest extends \PHPUnit_Framework_TestCase{
  const HOST = 'localhost';

  const PORT = 10000;

  /**
   * @var socket\Stream
   */
  protected $object;

  /**
   * @covers socket\Stream::close
   * @expectedException PHPUnit_Framework_Error_Warning
   */
  public function testClose(){
    $serverSocket = new socket\Socket();
    $clientSocket = new socket\Socket();
    $serverSocket->listen(self::HOST, self::PORT);
    $clientStream = $clientSocket->connect(self::HOST, self::PORT);
    $clientStream->close();
    $this->setExpectedException('\PPHP\tools\patterns\io\IOException');
    $clientStream->write('data');
    $serverSocket->shutdown();
  }

  /**
   * @covers socket\Stream::isClose
   */
  public function testIsClose(){
    $serverSocket = new socket\Socket();
    $clientSocket = new socket\Socket();
    $serverSocket->listen(self::HOST, self::PORT);
    $clientStream = $clientSocket->connect(self::HOST, self::PORT);
    $this->assertFalse($clientStream->isClose());
    $clientStream->close();
    $this->assertTrue($clientStream->isClose());
    $serverSocket->shutdown();
  }

  /**
   * @covers socket\Stream::read
   * @covers socket\Stream::write
   */
  public function testRead(){
    $serverSocket = new socket\Socket();
    $clientSocket = new socket\Socket();
    $serverSocket->listen(self::HOST, self::PORT);
    $clientStream = $clientSocket->connect(self::HOST, self::PORT);
    $serverStream = $serverSocket->accept();
    $clientStream->write('data');
    $this->assertEquals('d', $serverStream->read());
    $serverStream->read();
    $serverStream->read();
    $serverStream->read();
    $this->assertEquals('', $serverStream->read());
    $clientStream->close();
    $serverStream->close();
    $serverSocket->shutdown();
  }

  /**
   * @covers socket\Stream::readString
   */
  public function testReadString(){
    $serverSocket = new socket\Socket();
    $clientSocket = new socket\Socket();
    $serverSocket->listen(self::HOST, self::PORT);
    $clientStream = $clientSocket->connect(self::HOST, self::PORT);
    $serverStream = $serverSocket->accept();
    $clientStream->write('data');
    $this->assertEquals('da', $serverStream->readString(2));
    $this->assertEquals('ta', $serverStream->readString(4));
    $this->assertEquals('', $serverStream->readString(1));
    $clientStream->close();
    $serverStream->close();
    $serverSocket->shutdown();
  }

  /**
   * @covers socket\Stream::readLine
   */
  public function testReadLine(){
    $serverSocket = new socket\Socket();
    $clientSocket = new socket\Socket();
    $serverSocket->listen(self::HOST, self::PORT);
    $clientStream = $clientSocket->connect(self::HOST, self::PORT);
    $serverStream = $serverSocket->accept();
    $clientStream->write('data');
    $this->assertEquals('data', $serverStream->readLine());
    $this->assertEquals('', $serverStream->readLine());
    $clientStream->close();
    $serverStream->close();
    $serverSocket->shutdown();
  }

  /**
   * @covers socket\Stream::readAll
   */
  public function testReadAll(){
    $serverSocket = new socket\Socket();
    $clientSocket = new socket\Socket();
    $serverSocket->listen(self::HOST, self::PORT);
    $clientStream = $clientSocket->connect(self::HOST, self::PORT);
    $serverStream = $serverSocket->accept();
    $clientStream->write('data');
    $this->assertEquals('data', $serverStream->readAll());
    $this->assertEquals('', $serverStream->readAll());
    $clientStream->close();
    $serverStream->close();
    $serverSocket->shutdown();
  }

  /**
   * @covers socket\Stream::getIn
   */
  public function testGetIn(){
    $serverSocket = new socket\Socket();
    $clientSocket = new socket\Socket();
    $serverSocket->listen(self::HOST, self::PORT);
    $clientStream = $clientSocket->connect(self::HOST, self::PORT);
    $this->assertInstanceOf('\PPHP\tools\classes\standard\network\socket\inStream', $clientStream->getIn());
    $clientStream->close();
    $serverSocket->shutdown();
    $serverSocket->shutdown();
  }

  /**
   * @covers socket\Stream::getOut
   */
  public function testGetOut(){
    $serverSocket = new socket\Socket();
    $clientSocket = new socket\Socket();
    $serverSocket->listen(self::HOST, self::PORT);
    $clientStream = $clientSocket->connect(self::HOST, self::PORT);
    $this->assertInstanceOf('\PPHP\tools\classes\standard\network\socket\outStream', $clientStream->getOut());
    $clientStream->close();
    $serverSocket->shutdown();
  }
}
