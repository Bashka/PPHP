<?php
namespace PPHP\tests\tools\classes\standard\network\socket;

use \PPHP\tools\classes\standard\network\socket as socket;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class SocketTest extends \PHPUnit_Framework_TestCase{
  const HOST = 'localhost';

  const PORT = 10000;

  /**
   * @covers socket\Socket::connect
   * @covers socket\Socket::listen
   */
  public function testConnect(){
    $serverSocket = new socket\Socket;
    $serverSocket->listen(self::HOST, self::PORT);
    $clientSocket = new socket\Socket;
    $clientStream = $clientSocket->connect(self::HOST, self::PORT);
    $this->assertInstanceOf('\PPHP\tools\classes\standard\network\socket\Stream', $clientStream);
    $clientStream->close();
    $serverSocket->shutdown();
  }

  /**
   * @covers socket\Socket::accept
   */
  public function testAccept(){
    $serverSocket = new socket\Socket;
    $serverSocket->listen(self::HOST, self::PORT);
    $clientSocket = new socket\Socket;
    $clientStream = $clientSocket->connect(self::HOST, self::PORT);
    $serverStream = $serverSocket->accept();
    $this->assertInstanceOf('\PPHP\tools\classes\standard\network\socket\Stream', $serverStream);
    $clientStream->close();
    $serverStream->close();
    $serverSocket->shutdown();
  }
}
