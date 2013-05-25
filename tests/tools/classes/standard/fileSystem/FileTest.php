<?php
namespace PPHP\tests\tools\classes\standard\fileSystem;
use PPHP\tools\classes\standard\fileSystem as fileSystem;

spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});

class FileTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var fileSystem\File
   */
  protected $object;

  /**
   * Имя тестируемого файла в текущем каталоге.
   */
  const testFileName = 'testFile.txt';

  /**
   * Имя каталога, необходимого для целей тестирования.
   */
  const assistanceDir = 'testAssistanceDir';

  public static function setUpBeforeClass(){
    if(!file_exists(self::assistanceDir) || !is_dir(self::assistanceDir)){
      mkdir(self::assistanceDir);
    }
  }

  public static function tearDownAfterClass(){
    self::clearAssistanceDir();
    rmdir(self::assistanceDir);
  }

  protected function setUp(){
    $this->object = fileSystem\ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/tests/tools/classes/standard/fileSystem/' . self::testFileName);
  }

  protected function tearDown(){
    if(file_exists(self::testFileName) && is_file(self::testFileName)){
      unlink(self::testFileName);
    }
    if(file_exists('rename_' . self::testFileName) && is_file('rename_' . self::testFileName)){
      unlink('rename_' . self::testFileName);
    }
    self::clearAssistanceDir();
  }

  /**
   * @static
   * Метод отчищает ассистирующую директорию от содержимого.
   */
  private static function clearAssistanceDir(){
    $iterator = new \DirectoryIterator(self::assistanceDir);
    foreach($iterator as $component){
      if($component != '.' && $component != '..'){
        unlink(self::assistanceDir . '/' . $component);
      }
    }
  }

  /**
   * @covers fileSystem\File::getReader
   */
  public function testUpdateReader(){
    fclose(fopen(self::testFileName, 'a+'));
    $reader = $this->object->getReader();
    $reader->close();
    $this->assertTrue($reader !== ($newReader = $this->object->getReader()));
    $newReader->close();
  }

  /**
   * @covers fileSystem\File::getWriter
   */
  public function testUpdateWriter(){
    fclose(fopen(self::testFileName, 'a+'));
    $writer = $this->object->getWriter();
    $writer->close();
    $this->assertTrue($writer !== ($newWriter = $this->object->getWriter()));
    $newWriter->close();
  }

  /**
   * @covers fileSystem\File::create
   */
  public function testCreate(){
    $this->assertTrue($this->object->create());
    $this->assertTrue((file_exists(self::testFileName) && is_file(self::testFileName)));
  }

  /**
   * @covers fileSystem\File::create
   */
  public function testCreateIfFileExists(){
    fclose(fopen(self::testFileName, 'a+'));
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $this->object->create();
  }

  /**
   * @covers fileSystem\File::rename
   */
  public function testRename(){
    fclose(fopen(self::testFileName, 'a+'));
    $this->assertTrue($this->object->rename('rename_' . self::testFileName));
    $this->assertTrue((file_exists('rename_' . self::testFileName) && is_file('rename_' . self::testFileName)));
    $this->assertEquals('rename_' . self::testFileName, $this->object->getName());
  }

  /**
   * @covers fileSystem\File::rename
   */
  public function testRenameForDuplication(){
    fclose(fopen(self::testFileName, 'a+'));
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $this->object->rename(self::testFileName);
  }

  /**
   * @covers fileSystem\File::move
   */
  public function testMove(){
    fclose(fopen(self::testFileName, 'a+'));
    $assDir = fileSystem\ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/tests/tools/classes/standard/fileSystem/' . self::assistanceDir);
    $this->assertTrue($this->object->move($assDir));
    $this->assertTrue((!file_exists(self::testFileName) || !is_file(self::testFileName)));
    $this->assertTrue((file_exists(self::assistanceDir . '/' . self::testFileName) && is_file(self::assistanceDir . '/' . self::testFileName)));
    $this->assertEquals($_SERVER['DOCUMENT_ROOT'].'/PPHP/tests/tools/classes/standard/fileSystem/' . self::assistanceDir.'/'.self::testFileName, $this->object->getAddress());
  }

  /**
   * @covers fileSystem\File::move
   */
  public function testMoveForDuplication(){
    fclose(fopen(self::testFileName, 'a+'));
    fclose(fopen(self::assistanceDir . '/' . self::testFileName, 'a+'));
    $assDir = fileSystem\ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/tests/tools/classes/standard/fileSystem/' . self::assistanceDir);
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $this->object->move($assDir);
  }

  /**
   * @covers fileSystem\File::copyPaste
   */
  public function testCopyPaste(){
    fclose(fopen(self::testFileName, 'a+'));
    $assDir = fileSystem\ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/tests/tools/classes/standard/fileSystem/' . self::assistanceDir);
    $this->assertTrue($this->object->copyPaste($assDir));
    $this->assertTrue((file_exists(self::testFileName) && is_file(self::testFileName)));
    $this->assertTrue((file_exists(self::assistanceDir . '/' . self::testFileName) && is_file(self::assistanceDir . '/' . self::testFileName)));
  }

  /**
   * @covers fileSystem\File::copyPaste
   */
  public function testCopyPasteForDuplication(){
    fclose(fopen(self::testFileName, 'a+'));
    fclose(fopen(self::assistanceDir . '/' . self::testFileName, 'a+'));
    $assDir = fileSystem\ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/tests/tools/classes/standard/fileSystem/' . self::assistanceDir);
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $this->object->copyPaste($assDir);
  }

  /**
   * @covers fileSystem\File::getSize
   */
  public function testGetSizeForEmpty(){
    fclose(fopen(self::testFileName, 'a+'));
    $this->assertEquals(0, $this->object->getSize());
  }

  /**
   * @covers fileSystem\File::getSize
   */
  public function testGetSizeForNonEmpty(){
    $fileDescriptor = fopen(self::testFileName, 'a+');
    fwrite($fileDescriptor, 'test');
    fclose($fileDescriptor);
    $this->assertEquals(4, $this->object->getSize());
  }

  /**
   * @covers fileSystem\File::isExists
   */
  public function testIsExists(){
    $this->assertFalse($this->object->isExists());
    fclose(fopen(self::testFileName, 'a+'));
    $this->assertTrue($this->object->isExists());
  }

  /**
   * @covers fileSystem\File::getReader
   */
  public function testGetReader(){
    fclose(fopen(self::testFileName, 'a+'));
    $reader = $this->object->getReader();
    $this->assertInstanceOf('\PPHP\tools\classes\standard\fileSystem\io\BlockingFileReader', $reader);
    $reader->close();
  }

  /**
   * @covers fileSystem\File::getWriter
   */
  public function testGetWriter(){
    fclose(fopen(self::testFileName, 'a+'));
    $writer = $this->object->getWriter();
    $this->assertInstanceOf('\PPHP\tools\classes\standard\fileSystem\io\BlockingFileWriter', $writer);
    $writer->close();
  }

  /**
   * @covers fileSystem\File::getReader
   * @covers fileSystem\File::getWriter
   */
  public function testDoubleGetIO(){
    fclose(fopen(self::testFileName, 'a+'));
    $reader = $this->object->getReader();
    $this->assertEquals($reader, ($newReader = $this->object->getReader()));
    $reader->close();
  }

  /**
   * @covers fileSystem\File::delete
   */
  public function testDelete(){
    fclose(fopen(self::testFileName, 'a+'));
    $this->assertTrue($this->object->delete());
    $this->assertTrue((!file_exists(self::testFileName) || !is_file(self::testFileName)));
  }

  /**
   * @covers fileSystem\File::delete
   */
  public function testDeleteIfFileNonExists(){
    $this->setExpectedException('\PPHP\tools\classes\standard\fileSystem\NotExistsException');
    $this->object->delete();
  }

  /**
   * @covers fileSystem\File::getType
   */
  public function testGetType(){
    $this->assertEquals('txt', $this->object->getType());
  }
}
