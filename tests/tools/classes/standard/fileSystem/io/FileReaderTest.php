<?php
namespace PPHP\tests\tools\classes\standard\fileSystem\io;
use PPHP\tools\classes\standard\fileSystem\io as io;

spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});

class FileReaderTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var io\FileReader
   */
  protected $object;

  /**
   * Имя тестируемого файла в текущем каталоге.
   */
  const testFileName = 'testFile.txt';
  /**
   * Дескриптор тестируемого файла.
   * @var resource
   */
  static $descriptor;

  public static function setUpBeforeClass(){
    fclose(fopen(self::testFileName, 'a+'));
    self::$descriptor = fopen(self::testFileName, 'r+');
    fwrite(self::$descriptor, 'First test line'.PHP_EOL.'Second test line'.PHP_EOL);
  }

  public static function tearDownAfterClass(){
    fclose(self::$descriptor);
    unlink(self::testFileName);
  }

  protected function setUp(){
    $this->object = new io\FileReader(self::$descriptor);
    fseek(self::$descriptor, 0);
  }

  protected function tearDown(){
  }

  /**
   * @covers io\FileReader::read
   */
  public function testRead(){
    $this->object->read();
    $this->assertEquals('i', $this->object->read());
  }

  /**
   * @covers io\FileReader::read
   */
  public function testReadForEndFile(){
    $length = 31 + 2 * strlen(PHP_EOL);
    for($i = $length; $i--;){
      $this->object->read();
    }
    $this->assertEquals('', $this->object->read());
  }

  /**
   * @covers io\FileReader::readLine
   */
  public function testReadLine(){
    $this->object->read();
    $this->assertEquals('irst test line', $this->object->readLine());
  }

  /**
   * @covers io\FileReader::readLine
   */
  public function testReadLineForEndFile(){
    $length = 31 + 2 * strlen(PHP_EOL);
    for($i = $length; $i--;){
      $this->object->read();
    }
    $this->assertEquals('', $this->object->readLine());
  }

  /**
   * @covers io\FileReader::readString
   */
  public function testReadString(){
    $this->object->read();
    $this->assertEquals('irst ', $this->object->readString(5));
  }

  /**
   * @covers io\FileReader::readAll
   */
  public function testReadAll(){
    $this->object->read();
    $this->assertEquals('irst test line'.PHP_EOL.'Second test line'.PHP_EOL, $this->object->readAll());
  }

  /**
   * @covers io\FileReader::readString
   */
  public function testReadStringForEndFile(){
    $length = 31 + 2 * strlen(PHP_EOL);
    for($i = $length; $i--;){
      $this->object->read();
    }
    $this->assertEquals('', $this->object->readString(5));
  }

  /**
   * @covers io\FileSeekIO::setPosition
   */
  public function testSetPosition(){
    $this->assertTrue($this->object->setPosition(1));
    $this->assertEquals('i', $this->object->read());
  }

  /**
   * @covers io\FileSeekIO::getPosition
   */
  public function testGetPosition(){
    $this->object->read();
    $this->assertEquals(1, $this->object->getPosition());
  }

  /**
   * @covers io\FileClosed::close
   * @covers io\FileClosed::isClose
   */
  public function testClose(){
    $this->assertFalse($this->object->isClose());
    $this->assertTrue($this->object->close());
    $this->assertTrue($this->object->isClose());
  }
}
