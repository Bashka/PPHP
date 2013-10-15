<?php
namespace PPHP\tests\tools\classes\standard\network\protocols\applied\http;

use PPHP\tools\classes\standard\network\protocols\applied\http\Parameter;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')).'PPHP/dev/autoload/autoload.php';

class ParameterTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var Parameter
   */
  protected $object;

  protected function setUp(){
    $this->object = new Parameter('name', 'value');
  }

  /**
   * Должен устанавливать имя и значение праметра.
   * @covers \PPHP\tools\classes\standard\network\protocols\applied\http\Parameter::__construct
   */
  public function testShouldSetNameAndValue(){
    $p = new Parameter('name', 'value');
    $this->assertEquals('name', $p->getName());
    $this->assertEquals('value', $p->getValue());
  }

  /**
   * Должен возвращать имя параметра.
   * @covers \PPHP\tools\classes\standard\network\protocols\applied\http\Parameter::getName
   */
  public function testShouldReturnName(){
    $this->assertEquals('name', $this->object->getName());
  }

  /**
   * Должен возвращать значение параметра.
   * @covers \PPHP\tools\classes\standard\network\protocols\applied\http\Parameter::getValue
   */
  public function testShouldReturnValue(){
    $this->assertEquals('value', $this->object->getValue());
  }

  /**
   * Должен возвращать строку вида: имя:значение.
   * @covers \PPHP\tools\classes\standard\network\protocols\applied\http\Parameter::interpretation
   */
  public function testShouldInterpretation(){
    $this->assertEquals('name:value', $this->object->interpretation());
  }

  /**
   * Может быть восстановлен из сроки вида: имя:значение.
   * @covers \PPHP\tools\classes\standard\network\protocols\applied\http\Parameter::reestablish
   */
  public function testShouldRestorableForString(){
    $param = Parameter::reestablish('name:value');
    $this->assertEquals('name', $param->getName());
    $this->assertEquals('value', $param->getValue());

    $param = Parameter::reestablish('name:  value');
    $this->assertEquals('name', $param->getName());
    $this->assertEquals('value', $param->getValue());
  }

  /**
   * Допустимой строкой является строка вида: имя:значение.
   * @covers \PPHP\tools\classes\standard\network\protocols\applied\http\Parameter::isReestablish
   */
  public function testGoodString(){
    $this->assertTrue(Parameter::isReestablish('name:value'));
    $this->assertTrue(Parameter::isReestablish('name:  value'));
    $this->assertTrue(Parameter::isReestablish('name:value  '));
    $this->assertTrue(Parameter::isReestablish('1Na_m-e:v-*a4_lue'));
    $this->assertTrue(Parameter::isReestablish('name:'));
  }

  /**
   * Должен возвращать false при передаче строки недопустимой структуры.
   * @covers \PPHP\tools\classes\standard\network\protocols\applied\http\Parameter::isReestablish
   */
  public function testBedString(){
    $this->assertFalse(Parameter::isReestablish(':value'));
    $this->assertFalse(Parameter::isReestablish('namevalue'));
    $this->assertFalse(Parameter::isReestablish('name  :value'));
    $this->assertFalse(Parameter::isReestablish('  name:value'));
  }
}
