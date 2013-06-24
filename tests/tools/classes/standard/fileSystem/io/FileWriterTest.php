<?php
namespace PPHP\tests\tools\classes\standard\fileSystem\io;

use PPHP\tools\classes\standard\fileSystem\io as io;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class FileWriterTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var io\FileWriter
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
  }

  public static function tearDownAfterClass(){
    fclose(self::$descriptor);
    unlink(self::testFileName);
  }

  protected function setUp(){
    $this->object = new io\FileWriter(self::$descriptor);
    ftruncate(self::$descriptor, 0);
    fseek(self::$descriptor, 0);
    fwrite(self::$descriptor, "First test line\nSecond test line\n");
    fseek(self::$descriptor, 0);
  }

  protected function tearDown(){
  }

  /**
   * @covers io\FileWriter::write
   */
  public function testRewrite(){
    $this->assertEquals(5, $this->object->write('Rewri'));
    $this->assertEquals("Rewri test line\nSecond test line\n", file_get_contents(self::testFileName));
  }

  /**
   * @covers io\FileWriter::write
   */
  public function testWrite(){
    $this->object->setPosition(33);
    $this->assertEquals(3, $this->object->write('End'));
    $this->assertEquals("First test line\nSecond test line\nEnd", file_get_contents(self::testFileName));
  }

  /**
   * @covers io\FileWriter::clean
   */
  public function testClean(){
    $this->assertTrue($this->object->clean());
    $this->assertEquals('', file_get_contents(self::testFileName));
  }
}
