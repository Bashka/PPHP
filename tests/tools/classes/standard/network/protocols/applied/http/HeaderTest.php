<?php
namespace PPHP\tests\tools\classes\standard\network\protocols\applied\http;

use \PPHP\tools\classes\standard\network\protocols\applied\http as http;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class HeaderTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var http\Header
   */
  protected $object;

  protected function setUp(){
    $this->object = new http\Header();
  }

  /**
   * @covers http\Header::reestablish
   */
  public function testReestablish(){
    $header = http\Header::reestablish('nameA:valueA' . "\r\n" . 'nameB:valueB' . "\r\n");
    $this->assertEquals('valueA', $header->getParameter('nameA')->getValue());
    $this->assertEquals('valueB', $header->getParameter('nameB')->getValue());
    $header = http\Header::reestablish('nameA:valueA' . "\r\n");
    $this->assertEquals('valueA', $header->getParameter('nameA')->getValue());
    $header = http\Header::reestablish("\r\n");
    $this->assertEquals(0, count($header->getParameters()));
  }

  /**
   * @covers http\Header::isReestablish
   */
  public function testIsReestablish(){
    $this->assertTrue(http\Header::isReestablish("\r\n"));
    $this->assertTrue(http\Header::isReestablish('a:b' . "\r\n"));
    $this->assertTrue(http\Header::isReestablish('a:b' . "\r\n" . 'c:d' . "\r\n"));
    $this->assertFalse(http\Header::isReestablish(''));
    $this->assertFalse(http\Header::isReestablish('a:b'));
    $this->assertFalse(http\Header::isReestablish('a:b' . "\r\n" . 'c:d'));
    $this->assertFalse(http\Header::isReestablish('a:b' . "\r\n" . "\r\n"));
  }

  /**
   * @covers http\Header::addParameter
   * @covers http\Header::getParameter
   */
  public function testAddParameter(){
    $this->object->addParameter(new http\Parameter('name', 'value'));
    $this->assertEquals('value', $this->object->getParameter('name')->getValue());
  }

  /**
   * @covers http\Header::addParameterStr
   */
  public function testAddParameterStr(){
    $this->object->addParameterStr('name', 'value');
    $this->assertEquals('value', $this->object->getParameter('name')->getValue());
  }

  /**
   * @covers http\Header::hasParameter
   */
  public function testHasParameter(){
    $this->object->addParameterStr('name', 'value');
    $this->assertTrue($this->object->hasParameter('name'));
  }

  /**
   * @covers http\Header::interpretation
   */
  public function testInterpretation(){
    $this->object->addParameterStr('nameA', 'valueA');
    $this->object->addParameterStr('nameB', 'valueB');
    $this->assertEquals('nameA:valueA' . "\r\n" . 'nameB:valueB' . "\r\n", $this->object->interpretation());
  }

  /**
   * @covers http\Header::getParameters
   */
  public function testGetParameters(){
    $this->object->addParameterStr('nameA', 'valueA');
    $this->object->addParameterStr('nameB', 'valueB');
    $params = $this->object->getParameters();
    $this->assertEquals(2, count($params));
  }

  /**
   * @covers http\Header::getParameterValue
   */
  public function testGetParameterValue(){
    $this->object->addParameterStr('nameA', 'valueA');
    $this->object->addParameterStr('nameB', 'valueB');
    $this->assertEquals('valueA', $this->object->getParameterValue('nameA'));
  }
}
