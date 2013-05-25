<?php
namespace PPHP\tests\tools\patterns\io;
use \PPHP\tools\patterns\io as io;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class InStreamTest extends \PHPUnit_Framework_TestCase{
  /**
   * Имя файла, служащего входным потоком.
   */
  const fileName = 'file';
  /**
   * @var InStreamMock
   */
  protected $object;

  /**
   * Указатель входного потока.
   * @var
   */
  protected $fileDescriptor;

  /**
   * Метод устанавливает указанную строку в качестве входного потока и перемещает указатель на его начало.
   * @param $content Устанавливаемая строка.
   */
  protected function setFileContent($content){
    $this->tearDown();
    $this->fileDescriptor = fopen(self::fileName, 'w+');
    fwrite($this->fileDescriptor, $content);
    fseek($this->fileDescriptor, 0);
    $this->object = new InStreamMock($this->fileDescriptor);
  }

  protected function setUp(){
    $this->setFileContent('First string'.PHP_EOL.'Вторая строка'.PHP_EOL.'Last string');
  }

  protected function tearDown(){
    if(file_exists(self::fileName)){
      if($this->fileDescriptor !== null){
        fclose($this->fileDescriptor);
      }
      unlink(self::fileName);
    }
  }

  /**
   * @covers io\Reader::read
   * @covers InStreamMock::read
   */
  public function testRead(){
    $this->assertEquals('F', $this->object->read());
    $this->assertEquals('i', $this->object->read());

    $this->setFileContent('');
    $this->assertEquals('', $this->object->read());

    $this->setFileContent("\n");
    $this->assertEquals("\n", $this->object->read());
  }

  /**
   * @covers io\Reader::readString
   * @covers io\InStream::readString
   */
  public function testReadString(){
    $this->assertEquals('First', $this->object->readString(5));

    $this->setFileContent("Test\ntest");
    $this->assertEquals("Test\ntest", $this->object->readString(9));

    $this->setFileContent('');
    $this->assertEquals('', $this->object->readString(2));

    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    $this->object->readString(0);
  }

  /**
   * @covers io\Reader::readLine
   * @covers io\InStream::readLine
   */
  public function testReadLine(){
    $this->assertEquals('First string', $this->object->readLine());
    $this->assertEquals('Вторая строка', $this->object->readLine());
    $this->assertEquals('Last string', $this->object->readLine());

    $this->setFileContent('');
    $this->assertEquals('', $this->object->readLine());

    $this->setFileContent("\n");
    $this->assertEquals(false, $this->object->readLine());
  }

  /**
   * @covers io\Reader::readAll
   * @covers io\InStream::readAll
   */
  public function testReadAll(){
    $this->assertEquals('First string'.PHP_EOL.'Вторая строка'.PHP_EOL.'Last string', $this->object->readAll());
    $this->assertEquals('', $this->object->readAll());
  }
}
