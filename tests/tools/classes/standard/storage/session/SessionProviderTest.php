<?php
namespace PPHP\tests\tools\classes\standard\storage\session;

use PPHP\tools\classes\standard\storage\session\SessionProvider;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
session_start();
class SessionProviderTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var SessionProvider
   */
  protected $object;

  protected function setUp(){
    $this->object = SessionProvider::getInstance();
  }

  protected function tearDown(){
    $_SESSION = [];
  }

  public static function tearDownAfterClass(){
    $_SESSION = [];
    unset($_COOKIE[session_name()]);
    session_destroy();
  }

  /**
   * @covers SessionProvider::set
   */
  public function testSet(){
    $this->object->set('Test', 'Test');
    $this->assertEquals('Test', $_SESSION['Test']);
  }

  /**
   * @covers SessionProvider::get
   */
  public function testGet(){
    $_SESSION['Test'] = 'Test';
    $this->assertEquals('Test', $this->object->get('Test'));
  }

  /**
   * @covers SessionProvider::reset
   */
  public function testReset(){
    $_SESSION['Test'] = 'Test';
    $this->object->reset('Test');
    $this->assertFalse(isset($_SESSION['Test']));
  }

  /**
   * @covers SessionProvider::isExists
   */
  public function testIsExists(){
    $this->assertFalse($this->object->isExists('Test'));
    $_SESSION['Test'] = 'Test';
    $this->assertTrue($this->object->isExists('Test'));
  }
}
