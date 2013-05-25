<?php
namespace PPHP\tests\tools\classes\standard\baseType;
use \PPHP\tools\classes\standard\baseType\Boolean;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class BooleanTest extends \PHPUnit_Framework_TestCase {
  /**
   * @covers Boolean::__construct
   */
  public function test__construct(){
    new Boolean(true);
    new Boolean(false);

    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    new Boolean('true');
  }

  /**
   * @covers Boolean::reestablish
   */
  public function testReestablish(){
    $o = Boolean::reestablish('true');
    $this->assertEquals(true, $o->getVal());

    $o = Boolean::reestablish('false');
    $this->assertEquals(false, $o->getVal());
  }

  /**
   * @covers Boolean::isReestablish
   */
  public function testisReestablish(){
    $this->assertTrue(Boolean::isReestablish('true'));
    $this->assertTrue(Boolean::isReestablish('false'));

    $this->assertFalse(Boolean::isReestablish(''));
    $this->assertFalse(Boolean::isReestablish('t'));
  }
}
