<?php
namespace PPHP\tests\tools\classes\standard\fileSystem\io;

use PPHP\tools\classes\standard\fileSystem\io\FileReader;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class FileSeekIOTest extends \PHPUnit_Framework_TestCase{
  /**
   * Должен устанавливать позицию текущего байта.
   * @covers PPHP\tools\classes\standard\fileSystem\io\FileSeekIO::setPosition
   */
  public function testShouldSetCurrentByte(){
    $d = fopen('file', 'rb');
    $o = new FileReader($d);
    $o->setPosition(5);
    $this->assertEquals(5, ftell($d));
  }

  /**
   * Возвращает позицию текущего байта.
   * @covers PPHP\tools\classes\standard\fileSystem\io\FileSeekIO::getPosition
   */
  public function testShouldGetCurrentByte(){
    $d = fopen('file', 'rb');
    $o = new FileReader($d);
    fseek($d, 5);
    $this->assertEquals(5, $o->getPosition());
  }
}
