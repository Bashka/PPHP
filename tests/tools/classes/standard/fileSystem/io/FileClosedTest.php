<?php
namespace PPHP\tests\tools\classes\standard\fileSystem\io;

use PPHP\tools\classes\standard\fileSystem\io\FileReader;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class FileClosedTest extends \PHPUnit_Framework_TestCase{
  /**
   * Должен закрывать открытый дескриптор файла.
   * @covers PPHP\tools\classes\standard\fileSystem\io\FileClose::close
   */
  public function testShouldCloseDescriptor(){
    $d = fopen('file', 'rb');
    $o = new FileReader($d);
    $this->assertTrue($o->close());
    $this->assertFalse(is_resource($d));
  }

  /**
   * При повторном вызове на закрытом дескрипторе файла должен возвращать true.
   * @covers PPHP\tools\classes\standard\fileSystem\io\FileClose::close
   */
  public function testShouldReturnTrueIfDoubleCall(){
    $d = fopen('file', 'rb');
    $o = new FileReader($d);
    $this->assertTrue($o->close());
    $this->assertTrue($o->close());
  }

  /**
   * Должен возвращать true - если дескриптор файла закрыт, иначе - false.
   * @covers PPHP\tools\classes\standard\fileSystem\io\FileClose::isClose
   */
  public function testShouldReturnTrueIfDescriptorClose(){
    $d = fopen('file', 'rb');
    $o = new FileReader($d);
    $this->assertFalse($o->isClose());
    $o->close();
    $this->assertTrue($o->close());
  }
}
