<?php
namespace PPHP\tests\tools\classes\standard\storage\session;

use PPHP\tools\classes\standard\storage\session\SessionProvider;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')).'PPHP/dev/autoload/autoload.php';

ob_start();
class SessionProviderTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var SessionProvider
   */
  protected $object;

  public static function tearDownAfterClass(){
    ob_end_flush();
  }

  protected function setUp(){
    $this->object = SessionProvider::getInstance();
  }

  /**
   * Должен открывать сессию.
   * @covers PPHP\tools\classes\standard\storage\session\SessionProvider::start
   */
  public function testShouldOpenSession(){
    $this->object->start();
    $this->assertEquals([], $_SESSION);
    $this->object->destroy();
  }

  /**
   * Должен установить указанное имя сессии, если оно переданно.
   * @covers PPHP\tools\classes\standard\storage\session\SessionProvider::start
   */
  public function testShouldSetSessionName(){
    $this->object->start('MySession');
    $this->assertEquals('MySession', session_name());
    $this->object->destroy();
  }

  /**
   * Должен установить идентификатор сессии, если он передан.
   * @covers PPHP\tools\classes\standard\storage\session\SessionProvider::start
   */
  public function testShouldSetSessionID(){
    $this->object->start('MySession', 'sessionID');
    $this->assertEquals('sessionID', session_id());
    $this->object->destroy();
  }

  /**
   * Должен возвращать идентификатор сессии.
   * @covers PPHP\tools\classes\standard\storage\session\SessionProvider::getID
   */
  public function testShouldReturnIDSession(){
    $this->object->start('MySession', 'sessionID');
    $this->assertEquals('sessionID', $this->object->getID());
    $this->object->destroy();
  }

  /**
   * Должен возвращать пустую строку, если сессия не открыта.
   * @covers PPHP\tools\classes\standard\storage\session\SessionProvider::getID
   */
  public function testShouldReturnEmptyStringIfSessionClose(){
    $this->assertEquals('', $this->object->getID());
  }

  /**
   * Должен возвращать имя текущей сессии.
   * @covers PPHP\tools\classes\standard\storage\session\SessionProvider::getName
   */
  public function testShouldReturnSessionName(){
    $this->object->start('MySession');
    $this->assertEquals('MySession', $this->object->getName());
    $this->object->destroy();
  }

  /**
   * Должен возвращать пустую строку, если сессия не открыта.
   * @covers PPHP\tools\classes\standard\storage\session\SessionProvider::getName
   */
  public function testShouldReturnEmptyStringIfSessionClose2(){
    $this->assertEquals('', $this->object->getName());
  }

  /**
   * Должен закрывать сессию и уничтожать всю связанную с ней информацию.
   * @covers PPHP\tools\classes\standard\storage\session\SessionProvider::destroy
   */
  public function testShouldDestroySession(){
    $this->object->start();
    $this->object->destroy();
    $this->assertTrue(session_status() == PHP_SESSION_NONE);
  }

  /**
   * Должен устанавливать значение в сессии.
   * @covers PPHP\tools\classes\standard\storage\session\SessionProvider::set
   */
  public function testShouldSetValueSession(){
    $this->object->start();
    $this->object->set('key', 'value');
    $this->assertEquals('value', $_SESSION['key']);
    $this->object->destroy();
  }

  /**
   * Должен вернуть данные из сессии.
   * @covers PPHP\tools\classes\standard\storage\session\SessionProvider::get
   */
  public function testShouldGetValueSession(){
    $this->object->start();
    $_SESSION['key'] = 'value';
    $this->assertEquals('value', $this->object->get('key'));
    $this->object->destroy();
  }

  /**
   * Должен удалить данные из сессии.
   * @covers PPHP\tools\classes\standard\storage\session\SessionProvider::remove
   */
  public function testShouldRemoveValueSession(){
    $this->object->start();
    $_SESSION['key'] = 'value';
    $this->object->remove('key');
    $this->assertFalse(array_key_exists('key', $_SESSION));
    $this->object->destroy();
  }

  /**
   * Должен возвращать true - если заданный ключ установлен в сессии.
   * @covers PPHP\tools\classes\standard\storage\session\SessionProvider::isExists
   */
  public function testShouldReturnTrueIfValueExists(){
    $this->object->start();
    $this->assertFalse($this->object->isExists('key'));
    $_SESSION['key'] = 'value';
    $this->assertTrue($this->object->isExists('key'));
    $this->object->destroy();
  }

  /**
   * Должен вернуть данные из сессии.
   * @covers PPHP\tools\classes\standard\storage\session\SessionProvider::__set
   */
  public function testShouldSetValueSession2(){
    $this->object->start();
    $this->object->key = 'value';
    $this->assertEquals('value', $_SESSION['key']);
    $this->object->destroy();
  }

  /**
   * Должен устанавливать значение в сессии.
   * @covers PPHP\tools\classes\standard\storage\session\SessionProvider::__get
   */
  public function testShouldGetValueSession2(){
    $this->object->start();
    $_SESSION['key'] = 'value';
    $this->assertEquals('value', $this->object->key);
    $this->object->destroy();
  }

  /**
   * Должен удалить данные из сессии.
   * @covers PPHP\tools\classes\standard\storage\session\SessionProvider::__unset
   */
  public function testShouldRemoveValueSession2(){
    $this->object->start();
    $_SESSION['key'] = 'value';
    unset($this->object->key);
    $this->assertFalse(array_key_exists('key', $_SESSION));
    $this->object->destroy();
  }

  /**
   * Должен возвращать true - если заданный ключ установлен в сессии.
   * @covers PPHP\tools\classes\standard\storage\session\SessionProvider::__isset
   */
  public function testShouldReturnTrueIfValueExists2(){
    $this->object->start();
    $this->assertFalse(isset($this->object->key));
    $_SESSION['key'] = 'value';
    $this->assertTrue(isset($this->object->key));
    $this->object->destroy();
  }
}
