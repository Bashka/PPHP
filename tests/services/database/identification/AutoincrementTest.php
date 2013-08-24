<?php
namespace PPHP\tests\services\database\identification;

use PPHP\services\database\identification\Autoincrement;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class AutoincrementTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var Autoincrement
   */
  protected $object;

  protected function setUp(){
    $this->object = Autoincrement::getInstance();
  }

  protected function tearDown(){
    $this->object->resetOID();
  }

  /**
   * @covers Autoincrement::generateOID
   */
  public function testGenerateOID(){
    $this->assertEquals(1, $this->object->generateOID());
    $this->assertEquals(2, $this->object->generateOID());
  }

  /**
   * @covers Autoincrement::resetOID
   */
  public function testResetOID(){
    $this->assertEquals(1, $this->object->generateOID());
    $this->object->resetOID();
    $this->assertEquals(1, $this->object->generateOID());
  }
}
