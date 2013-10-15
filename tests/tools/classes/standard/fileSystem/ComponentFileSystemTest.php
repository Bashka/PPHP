<?php
namespace PPHP\tests\tools\classes\standard\fileSystem;

use PPHP\tools\classes\standard\fileSystem\Directory;
use PPHP\tools\classes\standard\fileSystem\File;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')).'PPHP/dev/autoload/autoload.php';

class ComponentFileSystemTest extends \PHPUnit_Framework_TestCase {
  /**
   * Должен изменять имя файла.
   * @covers PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::rename
   */
  public function testShouldRenameFile(){
    $f = new File('PPHP/tests/tools/classes/standard/fileSystem/file');
    $f->rename('renameFile');
    $this->assertEquals('renameFile', $f->getName());
    $this->assertTrue(file_exists($_SERVER['DOCUMENT_ROOT'].'/PPHP/tests/tools/classes/standard/fileSystem/renameFile'));
    $this->assertFalse(file_exists($_SERVER['DOCUMENT_ROOT'].'/PPHP/tests/tools/classes/standard/fileSystem/file'));
    $f->rename('file');
  }

  /**
   * Должен изменять имя каталога.
   * @covers PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::rename
   */
  public function testShouldRenameDir(){
    $d = new Directory('PPHP/tests/tools/classes/standard/fileSystem/dir');
    $d->rename('renameDir');
    $this->assertEquals('renameDir', $d->getName());
    $this->assertTrue(file_exists($_SERVER['DOCUMENT_ROOT'].'/PPHP/tests/tools/classes/standard/fileSystem/renameDir'));
    $this->assertFalse(file_exists($_SERVER['DOCUMENT_ROOT'].'/PPHP/tests/tools/classes/standard/fileSystem/dir'));
    $d->rename('dir');
  }

  /**
   * В качестве имени может выступать только не пустая строка.
   * @covers PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::rename
   */
  public function testNameShouldBeNonEmptyString(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    $f = new File('PPHP/tests/tools/classes/standard/fileSystem/file');
    $f->rename('');
  }

  /**
   * В имени не должно быть символа /.
   * @covers PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::rename
   */
  public function testNewNameNotBePath(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    $f = new File('PPHP/tests/tools/classes/standard/fileSystem/file');
    $f->rename('io/file');
  }

  /**
   * Переименовываемый компонент должен существовать.
   * @covers PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::rename
   */
  public function testComponentShouldExists(){
    $this->setExpectedException('PPHP\tools\classes\standard\fileSystem\NotExistsException');
    $f = new File('PPHP/tests/tools/classes/standard/fileSystem/notExistsFile');
    $f->rename('newFile');
  }

  /**
   * Должен перемещать файл.
   * @covers PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::move
   */
  public function testShouldMoveFile(){
    $f = new File('PPHP/tests/tools/classes/standard/fileSystem/file');
    $d = new Directory('PPHP/tests/tools/classes/standard/fileSystem/dir');
    $f->move($d);
    $this->assertEquals($d, $f->getLocation());
    $this->assertTrue(file_exists($_SERVER['DOCUMENT_ROOT'].'/PPHP/tests/tools/classes/standard/fileSystem/dir/file'));
    $this->assertFalse(file_exists($_SERVER['DOCUMENT_ROOT'].'/PPHP/tests/tools/classes/standard/fileSystem/file'));
    $f->move($d->getLocation());
  }

  /**
   * Должен перемещать каталог.
   * @covers PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::move
   */
  public function testShouldMoveDir(){
    $d = new Directory('PPHP/tests/tools/classes/standard/fileSystem/dir');
    $a = new Directory('PPHP/tests/tools/classes/standard/fileSystem/assistant');
    $d->move($a);
    $this->assertEquals($a, $d->getLocation());
    $this->assertTrue(file_exists($_SERVER['DOCUMENT_ROOT'].'/PPHP/tests/tools/classes/standard/fileSystem/assistant/dir'));
    $this->assertFalse(file_exists($_SERVER['DOCUMENT_ROOT'].'/PPHP/tests/tools/classes/standard/fileSystem/dir'));
    $d->move($a->getLocation());
  }

  /**
   * Должен исключать дублирование при перемещении.
   * @covers PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::move
   */
  public function testShouldExcludeDuplicate(){
    $d = new Directory('PPHP/tests/tools/classes/standard/fileSystem/dir');
    $l = $d->getLocation();
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $d->move($l);
  }

  /**
   * Перемещаемый компонент должен существовать.
   * @covers PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::move
   */
  public function testMoveComponentShouldExists(){
    $d = new Directory('PPHP/tests/tools/classes/standard/fileSystem/notExistsDir');
    $l = $d->getLocation();
    $this->setExpectedException('PPHP\tools\classes\standard\fileSystem\NotExistsException');
    $d->move($l);
  }

  /**
   * Должен исключать перемещение каталога в себя.
   * @covers PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::move
   */
  public function testShouldExcludeRecursiveMove(){
    $d = new Directory('PPHP/tests/tools/classes/standard/fileSystem/dir');
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\RuntimeException');
    $d->move($d);
  }

  /**
   * Должен возвращать каталог, содержащий вызываемый компонент.
   * @covers PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::getLocation
   */
  public function testShouldReturnLocationDir(){
    $d = new Directory('PPHP/tests/tools/classes/standard/fileSystem/dir');
    $l = new Directory('PPHP/tests/tools/classes/standard/fileSystem');
    $this->assertEquals($l->getAddress(), $d->getLocation()->getAddress());
  }

  /**
   * Должен возвращать имя вызываемого компонента.
   * @covers PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::getName
   */
  public function testShouldReturnNameComponent(){
    $d = new Directory('PPHP/tests/tools/classes/standard/fileSystem/dir');
    $this->assertEquals('dir', $d->getName());
  }

  /**
   * Должен возвращать полный адрес вызываемого компонента.
   * @covers PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::getAddress
   */
  public function testShouldReturnFullAddressComponent(){
    $d = new Directory('PPHP/tests/tools/classes/standard/fileSystem/dir');
    $this->assertEquals($_SERVER['DOCUMENT_ROOT'].'/PPHP/tests/tools/classes/standard/fileSystem/dir', $d->getAddress());
  }

  /**
   * Должен возвращать адрес родительского каталога.
   * @covers PPHP\tools\classes\standard\fileSystem\ComponentFileSystem::getLocationAddress
   */
  public function testShouldReturnLocationDirAddress(){
    $d = new Directory('PPHP/tests/tools/classes/standard/fileSystem/dir');
    $this->assertEquals($_SERVER['DOCUMENT_ROOT'].'/PPHP/tests/tools/classes/standard/fileSystem', $d->getLocationAddress());
  }
}
