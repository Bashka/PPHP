<?php
namespace PPHP\tests\tools\classes\standard\fileSystem\io;

use PPHP\tools\classes\standard\fileSystem\io\FileWriter;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class FileWriterTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var FileWriter
   */
  protected $object;

  /**
   * @var resource Дескриптор файла для теста.
   */
  protected $descriptor;

  protected function setUp(){
    $this->descriptor = fopen('file', 'r+b');
    $this->object = new FileWriter($this->descriptor);
  }

  protected function tearDown(){
    fclose($this->descriptor);
    $d = fopen('file', 'w');
    fwrite($d, 'Test data' . "\n" . 'Тестовые данные');
    fclose($d);
  }

  /**
   * Должен записывать указанный пакет байт в поток.
   * @covers PPHP\tools\classes\standard\fileSystem\io\FileWriter::write
   */
  public function testShouldWritePackageByte(){
    $this->object->write('Hello');
    $this->assertEquals('Hellodata' . "\n" . 'Тестовые данные', file_get_contents('file'));
  }

  /**
   * Должен возвращать число записанных байт.
   * @covers PPHP\tools\classes\standard\fileSystem\io\FileWriter::write
   */
  public function testShouldReturnRecordedByte(){
    $this->assertEquals(5, $this->object->write('Hello'));
  }

  /**
   * В качестве пакета байт может выступать только тип string.
   * @covers PPHP\tools\classes\standard\fileSystem\io\FileWriter::write
   */
  public function testPackageByteShouldBeString(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    $this->object->write(1);
  }

  /**
   * Должен удалять все содержимое потока.
   * @covers PPHP\tools\classes\standard\fileSystem\io\FileWriter::clear
   */
  public function testShouldClearStream(){
    $this->assertTrue($this->object->clean());
    $this->assertEquals('', file_get_contents('file'));
  }
}
