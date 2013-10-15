<?php
namespace PPHP\tests\tools\classes\standard\network\protocols\applied\http;


use PPHP\tools\classes\standard\network\protocols\applied\http\Header;
use PPHP\tools\classes\standard\network\protocols\applied\http\Parameter;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')).'PPHP/dev/autoload/autoload.php';

class HeaderTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var Header
   */
  protected $object;

  protected function setUp(){
    $this->object = new Header();
  }

  /**
   * Должен добавлять параметр в заголовок.
   * @covers \PPHP\tools\classes\standard\network\protocols\applied\http\Header::addParameter
   */
  public function testShouldAddParameter(){
    $this->object->addParameter(new Parameter('name', 'value'));
    $this->assertEquals('value', $this->object->getParameter('name')->getValue());
  }

  /**
   * Должен добавить параметр из строки.
   * @covers \PPHP\tools\classes\standard\network\protocols\applied\http\Header::addParameterStr
   */
  public function testShouldAddParameterFromString(){
    $this->object->addParameterStr('name', 'value');
    $this->assertEquals('value', $this->object->getParameter('name')->getValue());
  }

  /**
   * Возвращает true если данный параметр присутствует в заголовке.
   * @covers \PPHP\tools\classes\standard\network\protocols\applied\http\Header::hasParameter
   */
  public function testShouldReturnTrueIfParameterExists(){
    $this->object->addParameterStr('name', 'value');
    $this->assertTrue($this->object->hasParameter('name'));
    $this->assertFalse($this->object->hasParameter('key'));
  }

  /**
   * Должен вернуть массив всех имеющихся в заголовке параметров.
   * @covers \PPHP\tools\classes\standard\network\protocols\applied\http\Header::getParameters
   */
  public function testShouldReturnParameters(){
    $this->assertEquals([], $this->object->getParameters());

    $this->object->addParameter(new Parameter('name', 'value'));
    $this->assertEquals(1, count($this->object->getParameters()));
  }

  /**
   * Должен вернуть указанный параметр.
   * @covers \PPHP\tools\classes\standard\network\protocols\applied\http\Header::getParameter
   */
  public function testShouldReturnParameter(){
    $this->object->addParameter(new Parameter('name', 'value'));
    $this->assertInstanceOf('PPHP\tools\classes\standard\network\protocols\applied\http\Parameter', $this->object->getParameter('name'));
  }

  /**
   * Должен вернуть null если зарпашиваемый параметр отсутствует в заголовке.
   * @covers \PPHP\tools\classes\standard\network\protocols\applied\http\Header::getParameter
   */
  public function testShouldReturnNullIfParameterNotExists(){
    $this->assertEquals(null, $this->object->getParameter('name'));
  }

  /**
   * Должен вернуть значение указанного параметра.
   * @covers \PPHP\tools\classes\standard\network\protocols\applied\http\Header::getParameterValue
   */
  public function testShouldReturnParameterValue(){
    $this->object->addParameter(new Parameter('name', 'value'));
    $this->assertEquals('value', $this->object->getParameterValue('name'));
  }

  /**
   * Должен вернуть null если зарпашиваемый параметр отсутствует в заголовке.
   * @covers \PPHP\tools\classes\standard\network\protocols\applied\http\Header::getParameterValue
   */
  public function testShouldReturnNullIfParameterNotExists2(){
    $this->assertEquals(null, $this->object->getParameterValue('name'));
  }

  /**
   * Должен возвращать строку вида: имя:значение<driver>...
   * @covers \PPHP\tools\classes\standard\network\protocols\applied\http\Header::interpretation
   */
  public function testShouldInterpretation(){
    $this->object->addParameterStr('nameA', 'valueA');
    $this->object->addParameterStr('nameB', 'valueB');
    $this->assertEquals('nameA:valueA' . "\r\n" . 'nameB:valueB' . "\r\n", $this->object->interpretation());
  }

  /**
   * Может быть восстановлен из сроки вида: имя:значение<driver>...
   * @covers \PPHP\tools\classes\standard\network\protocols\applied\http\Header::reestablish
   */
  public function testShouldRestorableForString(){
    $header = Header::reestablish('nameA:valueA' . "\r\n" . 'nameB:valueB' . "\r\n");
    $this->assertEquals('valueA', $header->getParameter('nameA')->getValue());
    $this->assertEquals('valueB', $header->getParameter('nameB')->getValue());

    $header = Header::reestablish('nameA:valueA' . "\r\n");
    $this->assertEquals('valueA', $header->getParameter('nameA')->getValue());

    $header = Header::reestablish("\r\n");
    $this->assertEquals(0, count($header->getParameters()));
  }

  /**
   * Допустимой строкой является строка вида: имя:значение<driver>...
   * @covers \PPHP\tools\classes\standard\network\protocols\applied\http\Header::isReestablish
   */
  public function testGoodString(){
    $this->assertTrue(Header::isReestablish("\r\n"));
    $this->assertTrue(Header::isReestablish('a:b' . "\r\n"));
    $this->assertTrue(Header::isReestablish('a:b' . "\r\n" . 'c:d' . "\r\n"));
  }

  /**
   * Должен возвращать false при передаче строки недопустимой структуры.
   * @covers \PPHP\tools\classes\standard\network\protocols\applied\http\Header::isReestablish
   */
  public function testBedString(){
    $this->assertFalse(Header::isReestablish(''));
    $this->assertFalse(Header::isReestablish('a:b'));
    $this->assertFalse(Header::isReestablish('a:b' . "\r\n" . 'c:d'));
    $this->assertFalse(Header::isReestablish('a:b' . "\r\n" . "\r\n"));
  }
}
