<?php
namespace PPHP\tests\tools\classes\standard\fileSystem\io;
use PPHP\tests\tools\classes\standard\fileSystem\TestObserver;
use PPHP\tools\classes\standard\fileSystem\io as io;

spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});

class BlockingFileTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var io\BlockingFileReader
   */
  protected $object;

  /**
   * @var TestObserver
   */
  protected $observer;

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
  }

  public static function tearDownAfterClass(){
    unlink(self::testFileName);
  }

  protected function setUp(){
    self::$descriptor = fopen(self::testFileName, 'r+');
    $this->object = new io\BlockingFileReader(self::$descriptor);
    $this->observer = new TestObserver();
    $this->object->attach($this->observer);
  }

  /**
   * @covers PPHP\tools\patterns\observer\TSubject::attach
   * @covers PPHP\tools\patterns\observer\TSubject::notify
   * @covers io\BlockingFileReader::close
   * @covers io\BlockingFileWriter::close
   */
  public function testClose(){
    $this->assertTrue($this->object->close());
    $this->assertTrue($this->observer->getUpdating());
  }
}
