<?php
namespace PPHP\tests\tools\classes\standard\network\protocols\applied\http;
use \PPHP\tools\classes\standard\network\protocols\applied\http as http;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class ParameterTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var http\Parameter
   */
  protected $object;

  protected function setUp(){
    $this->object = new http\Parameter('name', 'value');
  }

  /**
   * @covers http\Parameter::reestablish
   */
  public function testReestablish(){
    $param = http\Parameter::reestablish('name:value');
    $this->assertEquals('name', $param->getName());
    $this->assertEquals('value', $param->getValue());

    $param = http\Parameter::reestablish('name:  value');
    $this->assertEquals('name', $param->getName());
    $this->assertEquals('value', $param->getValue());

    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\StructureException');
    http\Parameter::reestablish('name');
  }

  /**
   * @covers http\Parameter::isReestablish
   */
  public function testIsReestablish(){
    $this->assertTrue(http\Parameter::isReestablish('a:b'));
    $this->assertTrue(http\Parameter::isReestablish('a:1'));
    $this->assertTrue(http\Parameter::isReestablish('HEllo:world_'));
    $this->assertTrue(http\Parameter::isReestablish('a:'));

    $this->assertFalse(http\Parameter::isReestablish(':b'));
    $this->assertFalse(http\Parameter::isReestablish('ab'));
    $this->assertFalse(http\Parameter::isReestablish('a:
    b'));
  }

  /**
   * @covers http\Parameter::interpretation
   */
  public function testInterpretation(){
    $this->assertEquals('name:value', $this->object->interpretation());
  }

  /**
   * @covers http\Parameter::getName
   */
  public function testGetName(){
    $this->assertEquals('name', $this->object->getName());
  }

  /**
   * @covers http\Parameter::getValue
   */
  public function testGetValue(){
    $this->assertEquals('value', $this->object->getValue());
  }
}
