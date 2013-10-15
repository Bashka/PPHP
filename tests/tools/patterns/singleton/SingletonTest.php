<?php
namespace PPHP\tests\tools\patterns\singleton;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class SingletonTest extends \PHPUnit_Framework_TestCase{
  /**
   * Должен возвращать экземпляр класса.
   * @covers \PPHP\tools\patterns\singleton\TSingleton::getInstance
   */
  public function testShouldReturnObjectClass(){
    $this->assertInstanceOf('\PPHP\tests\tools\patterns\singleton\SingletonMock', SingletonMock::getInstance());
  }

  /**
   * Повторный вызов должен возвращать экземпляр? созданный при первом вызове.
   * @covers \PPHP\tools\patterns\singleton\TSingleton::getInstance
   */
  public function testShouldReturnFirstObject(){
    $o = SingletonMock::getInstance();
    $this->assertEquals($o, SingletonMock::getInstance());
    $o = ChildSingletonMock::getInstance();
    $this->assertEquals($o, ChildSingletonMock::getInstance());
  }

  /**
   * Должен возвращать различные экземпляры для различных классов в одной иерархии наследования.
   * @covers \PPHP\tools\patterns\singleton\TSingleton::getInstance
   */
  public function testShouldReturnChildrenObjects(){
    $po = SingletonMock::getInstance();
    $co = ChildSingletonMock::getInstance();
    $this->assertTrue($po !== $co);
  }

  /**
   * Должен выбрасывать исключение.
   * @covers \PPHP\tools\patterns\singleton\TSingleton::__clone
   */
  public function testShouldThrowException(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\RuntimeException');
    $instance = SingletonMock::getInstance();
    $instance = clone $instance;
  }
}
