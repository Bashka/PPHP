<?php
namespace PPHP\tests\tools\patterns\database\identification;

use PPHP\tools\patterns\database\identification\TOID;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class OIDTest extends \PHPUnit_Framework_TestCase{
  /**
   * Для неидентифицированного объекта должен возвращать null.
   * @covers \PPHP\tools\patterns\database\identification\TOID::getOID
   */
  public function testShouldReturnNullForNoIdentify(){
    $o = new OIDMock;
    $this->assertEquals(null, $o->getOID());
  }

  /**
   * Для идентифицированного объекта должен возвращать идентификатор объекта.
   * @covers \PPHP\tools\patterns\database\identification\TOID::getOID
   */
  public function testShouldReturnOIDForIdentify(){
    $o = new OIDMock;
    $o->setOID(1);
    $this->assertEquals(1, $o->getOID());
  }

  /**
   * Не идентифицированному объекту должен присвоить идентификатор.
   * @covers \PPHP\tools\patterns\database\identification\TOID::setOID
   */
  public function testShouldSetOIDForNoIdentify(){
    $o = new OIDMock;
    $o->setOID(1);
    $this->assertEquals(1, $o->getOID());
  }

  /**
   * В качестве идентификатора может выступать только целое число, большее нуля.
   * @covers \PPHP\tools\patterns\database\identification\TOID::setOID
   */
  public function testOIDShouldBePositiveInteger(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    $o = new OIDMock;
    $o->setOID(0);
  }

  /**
   * В качестве идентификатора не может выступать строка
   * @covers \PPHP\tools\patterns\database\identification\TOID::setOID
   */
  public function testOIDCanNotBeString(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    $o = new OIDMock;
    $o->setOID('1');
  }

  /**
   * Объект с идентификатором нельзя идентифицировать повторно.
   * @covers \PPHP\tools\patterns\database\identification\TOID::setOID
   */
  public function testShouldThrowExceptionForIdentify(){
    $this->setExpectedException('\PPHP\tools\patterns\database\identification\OIDException');
    $o = new OIDMock;
    $o->setOID(1);
    $o->setOID(1);
  }

  /**
   * Для идентифицированного объекта должен возвращать true.
   * @covers \PPHP\tools\patterns\database\identification\TOID::isOID
   */
  public function testShouldReturnTrueForIdentify(){
    $o = new OIDMock;
    $o->setOID(1);
    $this->assertTrue($o->isOID());
  }

  /**
   * Для не идентифицированного объекта должен возвращать false.
   * @covers \PPHP\tools\patterns\database\identification\TOID::isOID
   */
  public function testShouldReturnFalseForNoIdentify(){
    $o = new OIDMock;
    $this->assertFalse($o->isOID());
  }

  /**
   * Должен создавать объект вызываемого класса с установленным идентификатором.
   * @covers \PPHP\tools\patterns\database\identification\TOID::getProxy
   */
  public function testShouldReturnProxy(){
    $o = OIDMock::getProxy(1);
    $this->assertEquals(1, $o->getOID());
  }
}
