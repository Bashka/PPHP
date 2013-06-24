<?php
namespace PPHP\tests\tools\classes\standard\baseType\exceptions;

use PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class InvalidArgumentExceptionTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers InvalidArgumentException::verifyType
   */
  public function testVerifyType(){
    InvalidArgumentException::verifyType(null, 'n');
    InvalidArgumentException::verifyType('', 's');
    InvalidArgumentException::verifyType('a', 'S');
    InvalidArgumentException::verifyType(1, 'i');
    InvalidArgumentException::verifyType(1.1, 'f');
    InvalidArgumentException::verifyType(true, 'b');
    InvalidArgumentException::verifyType(true, 'bn');
    InvalidArgumentException::verifyType('a', 'sif');
    InvalidArgumentException::verifyType(5, 'fi');
    InvalidArgumentException::verifyType(null, 'Sn');
  }

  /**
   * @covers InvalidArgumentException::verifyType
   */
  public function testVerifyTypeExceptions(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    InvalidArgumentException::verifyType(1, 'n');
    InvalidArgumentException::verifyType(1, 's');
    InvalidArgumentException::verifyType('', 'S');
    InvalidArgumentException::verifyType(1.1, 'i');
    InvalidArgumentException::verifyType(1, 'f');
    InvalidArgumentException::verifyType(null, 'b');
    InvalidArgumentException::verifyType(1, 'bn');
    InvalidArgumentException::verifyType(true, 'sif');
    InvalidArgumentException::verifyType('a', 'fi');
    InvalidArgumentException::verifyType('', 'Sn');
  }

  /**
   * @covers InvalidArgumentException::verifyVal
   */
  public function testVerifyVal(){
    InvalidArgumentException::verifyVal(5, 'i == 5');
    InvalidArgumentException::verifyVal('test', 's eq test');
    InvalidArgumentException::verifyVal([1, 2, 3], 'a > 2');
  }

  /**
   * @covers InvalidArgumentException::getTypeException
   */
  public function testGetTypeException(){
    $this->assertEquals('Недопустимый тип параметра. Ожидается [string] вместо [integer].', InvalidArgumentException::getTypeException('string', 'integer')->getMessage());
    $this->assertEquals('Недопустимый тип параметра. Ожидается [string|integer] вместо [array].', InvalidArgumentException::getTypeException(['string', 'integer'], 'array')->getMessage());
  }

  /**
   * @covers InvalidArgumentException::getValidException
   */
  public function testGetValidException(){
    $this->assertEquals('Недопустимое значение параметра. Ожидается соответствие маске [> 0] вместо [0].', InvalidArgumentException::getValidException('> 0', 0)->getMessage());
  }
}
