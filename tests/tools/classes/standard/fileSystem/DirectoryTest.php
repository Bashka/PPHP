<?php
namespace PPHP\tests\tools\classes\standard\fileSystem;

use PPHP\tools\classes\standard\fileSystem\ComponentFileSystem;
use PPHP\tools\classes\standard\fileSystem as fileSystem;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class DirectoryTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var fileSystem\Directory
   */
  protected $object;

  /**
   * Имя тестируемого каталога в текущем каталоге.
   */
  const testDirName = 'testDir';

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
    if(file_exists(self::assistanceDir) && is_dir(self::assistanceDir)){
      self::clearDir(self::assistanceDir);
      rmdir(self::assistanceDir);
    }
  }

  protected function setUp(){
    $this->object = ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/tests/tools/classes/standard/fileSystem/' . self::testDirName);
  }

  protected function tearDown(){
    self::clearDir(self::assistanceDir);
    if(file_exists(self::testDirName) && is_dir(self::testDirName)){
      self::clearDir(self::testDirName);
      rmdir(self::testDirName);
    }
    if(file_exists('rename_' . self::testDirName) && is_dir('rename_' . self::testDirName)){
      rmdir('rename_' . self::testDirName);
    }
  }

  /**
   * @static
   * Метод отчищает директорию от содержимого.
   * @param string $dirAddress Адрес отчищаемой директории относительно текущего каталога.
   */
  private static function clearDir($dirAddress){
    $iterator = new \DirectoryIterator($dirAddress);
    foreach($iterator as $component){
      if($component != '.' && $component != '..'){
        if(is_file($dirAddress . '/' . $component)){
          unlink($dirAddress . '/' . $component);
        }
        else{
          self::clearDir($dirAddress . '/' . $component);
          rmdir($dirAddress . '/' . $component);
        }
      }
    }
  }

  private static function createSubcomponent(){
    if(!file_exists(self::testDirName) || !is_dir(self::testDirName)){
      mkdir(self::testDirName);
    }
    fclose(fopen(self::testDirName . '/testFile.txt', 'a+'));
    mkdir(self::testDirName . '/testDir');
    fclose(fopen(self::testDirName . '/testDir/testFile.txt', 'a+'));
  }

  /**
   * @covers fileSystem\Directory::rename
   */
  public function testRename(){
    mkdir(self::testDirName);
    $this->assertTrue($this->object->rename('rename_' . self::testDirName));
    $isRename = (file_exists('rename_' . self::testDirName) && is_dir('rename_' . self::testDirName) && !file_exists(self::testDirName));
    $this->assertTrue($isRename);
  }

  /**
   * @covers fileSystem\Directory::rename
   */
  public function testRenameForDuplication(){
    mkdir(self::testDirName);
    mkdir('rename_' . self::testDirName);
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $this->object->rename('rename_' . self::testDirName);
  }

  /**
   * @covers fileSystem\Directory::move
   */
  public function testMove(){
    self::createSubcomponent();
    $assDir = ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/tests/tools/classes/standard/fileSystem/' . self::assistanceDir);
    $this->assertTrue($this->object->move($assDir));
    $this->assertTrue((!file_exists(self::testDirName) || !is_dir(self::testDirName)));
    $this->assertEquals($_SERVER['DOCUMENT_ROOT'] . '/PPHP/tests/tools/classes/standard/fileSystem/' . self::assistanceDir . '/' . self::testDirName, $this->object->getAddress());
    $newAddress = self::assistanceDir . '/' . self::testDirName;
    $this->assertTrue((file_exists($newAddress) && is_dir($newAddress)));
    $isMultiMove = (file_exists($newAddress . '/testFile.txt') && file_exists($newAddress . '/testDir') && file_exists($newAddress . '/testDir/testFile.txt'));
    $this->assertTrue($isMultiMove);
  }

  /**
   * @covers fileSystem\Directory::move
   */
  public function testMoveForDuplication(){
    mkdir(self::testDirName);
    mkdir(self::assistanceDir . '/' . self::testDirName);
    $assDir = ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/tests/tools/classes/standard/fileSystem/' . self::assistanceDir);
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $this->object->move($assDir);
  }

  /**
   * @covers fileSystem\Directory::isExists
   */
  public function testIsExists(){
    $this->assertFalse($this->object->isExists());
    mkdir(self::testDirName);
    $this->assertTrue($this->object->isExists());
  }

  /**
   * @covers fileSystem\Directory::copyPaste
   */
  public function testCopyPaste(){
    self::createSubcomponent();
    $assDir = ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/tests/tools/classes/standard/fileSystem/' . self::assistanceDir);
    $this->assertTrue($this->object->copyPaste($assDir));
    $this->assertTrue((file_exists(self::testDirName) && is_dir(self::testDirName)));
    $newAddress = self::assistanceDir . '/' . self::testDirName;
    $this->assertTrue((file_exists($newAddress) && is_dir($newAddress)));
    $isMultiCopy = (file_exists($newAddress . '/testFile.txt') && file_exists($newAddress . '/testDir') && file_exists($newAddress . '/testDir/testFile.txt'));
    $this->assertTrue($isMultiCopy);
  }

  /**
   * @covers fileSystem\Directory::copyPaste
   */
  public function testCopyPasteForDuplication(){
    mkdir(self::testDirName);
    mkdir(self::assistanceDir . '/' . self::testDirName);
    $assDir = ComponentFileSystem::constructDirFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/tests/tools/classes/standard/fileSystem/' . self::assistanceDir);
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $this->object->copyPaste($assDir);
  }

  /**
   * @covers fileSystem\Directory::delete
   */
  public function testDelete(){
    mkdir(self::testDirName);
    $this->assertTrue($this->object->delete());
    $this->assertTrue((!file_exists(self::testDirName) || !is_dir(self::testDirName)));
  }

  /**
   * @covers fileSystem\Directory::delete
   */
  public function testDeleteIfDirNonExists(){
    $this->setExpectedException('\PPHP\tools\classes\standard\fileSystem\NotExistsException');
    $this->object->delete();
  }

  /**
   * @covers fileSystem\Directory::getSize
   */
  public function testGetSizeForEmpty(){
    self::createSubcomponent();
    $this->assertEquals(0, $this->object->getSize());
  }

  /**
   * @covers fileSystem\Directory::getSize
   */
  public function testGetSizeForNonEmpty(){
    self::createSubcomponent();
    $d = fopen(self::testDirName . '/testFile.txt', 'a+');
    fwrite($d, '1');
    fclose($d);
    $d = fopen(self::testDirName . '/testDir/testFile.txt', 'a+');
    fwrite($d, '1');
    fclose($d);
    $this->assertEquals(2, $this->object->getSize());
  }

  /**
   * @covers fileSystem\Directory::create
   */
  public function testCreate(){
    $this->assertTrue($this->object->create());
    $this->assertTrue((file_exists(self::testDirName) && is_dir(self::testDirName)));
  }

  /**
   * @covers fileSystem\Directory::create
   */
  public function testCreateIfFileExists(){
    mkdir(self::testDirName);
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $this->object->create();
  }

  /**
   * @covers fileSystem\Directory::getDirectoryIterator
   */
  public function testGetDirectoryIterator(){
    mkdir(self::testDirName);
    $this->assertInstanceOf('\DirectoryIterator', $this->object->getDirectoryIterator());
  }

  /**
   * @covers fileSystem\Directory::getFile
   */
  public function testGetFile(){
    self::createSubcomponent();
    $this->assertInstanceOf('\PPHP\tools\classes\standard\fileSystem\File', $this->object->getFile('testFile.txt'));
  }

  /**
   * @covers fileSystem\Directory::getDir
   */
  public function testGetDir(){
    self::createSubcomponent();
    $this->assertInstanceOf('\PPHP\tools\classes\standard\fileSystem\Directory', $this->object->getDir('testDir'));
  }

  /**
   * @covers fileSystem\Directory::getNamesComponents
   */
  public function testGetNamesComponents(){
    self::createSubcomponent();
    $this->assertTrue(is_array($this->object->getNamesComponents()));
  }

  /**
   * @covers fileSystem\Directory::isFileExists
   */
  public function testIsFileExists(){
    mkdir(self::testDirName);
    $this->assertFalse($this->object->isFileExists('testFile.txt'));
    self::createSubcomponent();
    $this->assertTrue($this->object->isFileExists('testFile.txt'));
  }

  /**
   * @covers fileSystem\Directory::isDirExists
   */
  public function testIsDirExists(){
    mkdir(self::testDirName);
    $this->assertFalse($this->object->isDirExists('testDir'));
    self::createSubcomponent();
    $this->assertTrue($this->object->isDirExists('testDir'));
  }

  /**
   * @covers fileSystem\Directory::clear
   */
  public function testClear(){
    self::createSubcomponent();
    $this->object->clear();
    $this->assertTrue((!file_exists(self::testDirName . '/testFile.txt') && !file_exists(self::testDirName . '/testDir')));
  }
}
