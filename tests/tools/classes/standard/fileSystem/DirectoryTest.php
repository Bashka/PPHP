<?php
namespace PPHP\tests\tools\classes\standard\fileSystem;

use PPHP\tools\classes\standard\fileSystem\Directory;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')).'PPHP/dev/autoload/autoload.php';

class DirectoryTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var Directory
   */
  private $object;

  protected function setUp(){
    $this->object = new Directory('PPHP/tests/tools/classes/standard/fileSystem/dir');
  }

  /**
   * Компонента с данным именем в родительском каталоге не должно существовать.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::rename
   */
  public function testShouldPreventDuplicationOfRename(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $this->object->rename('assistant');
  }

  /**
   * В аргументе не должно присутствовать символа слеша.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::rename
   */
  public function testShouldThrowExceptionIfNewNameIsAddress(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    $this->object->rename('dir/assistant');
  }

  /**
   * Должен возвращать true если вызываемый каталог существует в файловой системе, иначе - false.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::isExists
   */
  public function testShouldReturnTrueIfDirExists(){
    $this->assertTrue($this->object->isExists());

    $d = new Directory('PPHP/tests/tools/classes/standard/fileSystem/notExistsDir');
    $this->assertFalse($d->isExists());
  }

  /**
   * Должен выбрасывать исключение если родительского каталога не существует.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::isExists
   */
  public function testShouldThrowExceptionIfNotExistsParentDir(){
    $this->setExpectedException('PPHP\tools\classes\standard\fileSystem\NotExistsException');
    $d = new Directory('PPHP/tests/tools/classes/standard/fileSystem/notExistsDir/dir');
    $d->isExists();
  }

  /**
   * Должен выполнять копирование каталога со всеми вложенными файлами.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::copyPaste
   */
  public function testShouldCopyDirAndAllChildComponents(){
    $assDir = new Directory('PPHP/tests/tools/classes/standard/fileSystem/assistant');
    $copy = $this->object->copyPaste($assDir);

    $this->assertTrue((file_exists('dir') && is_dir('dir')));

    $newAddress = 'PPHP/tests/tools/classes/standard/fileSystem/assistant/dir';
    $this->assertTrue((file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$newAddress) && is_dir($_SERVER['DOCUMENT_ROOT'].'/'.$newAddress)));

    $this->assertTrue(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$newAddress . '/fileChild') && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$newAddress . '/dir'));

    $copy->delete();
  }

  /**
   * Должен возвращать представление созданной копии.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::copyPaste
   */
  public function testShouldReturnCopy(){
    $assDir = new Directory('PPHP/tests/tools/classes/standard/fileSystem/assistant');
    $copy = $this->object->copyPaste($assDir);
    $this->assertInstanceOf('PPHP\tools\classes\standard\fileSystem\Directory', $copy);
    $copy->delete();
  }

  /**
   * Должен выбрасывать исключение если вызываемого каталога нет в файловой системе.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::copyPaste
   */
  public function testShouldThrowExceptionIfDirNotExists(){
    $this->setExpectedException('PPHP\tools\classes\standard\fileSystem\NotExistsException');
    $dir = new Directory('PPHP/tests/tools/classes/standard/fileSystem/notExistsDir');
    $dir->copyPaste($this->object);
  }

  /**
   * Должен предотвращать дублирование.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::copyPaste
   */
  public function testShouldPreventDuplicationOfCopy(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $this->object->copyPaste($this->object);
  }

  /**
   * Должен удалять каталог и все вложенные компоненты.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::delete
   */
  public function testShouldRecursivelyRemoveDir(){
    $this->assertTrue($this->object->delete());
    $this->assertTrue((!file_exists('dir') || !is_dir('dir')));
    $this->assertTrue((!file_exists('dir/dir') || !is_dir('dir/dir')));

    $this->object->create();
    $this->object->createDir('dir');
    $f = $this->object->createFile('fileChild');
    $w = $f->getWriter();
    $w->write('Test contend');
    $w->close();
  }

  /**
   * Должен выбрасывать исключение если вызываемого каталога нет в файловой системе.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::delete
   */
  public function testShouldThrowExceptionIfDirNotExists2(){
    $this->setExpectedException('PPHP\tools\classes\standard\fileSystem\NotExistsException');
    $dir = new Directory('PPHP/tests/tools/classes/standard/fileSystem/notExistsDir');
    $dir->delete();
  }

  /**
   * Должен удалять все компоненты в вызываемом каталоге.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::clear
   */
  public function testShouldRemoveAllChild(){
    $this->object->clear();
    $this->assertTrue((!file_exists('dir/dir') && !file_exists('dir/fileChild')));

    $this->object->createDir('dir');
    $f = $this->object->createFile('fileChild');
    $w = $f->getWriter();
    $w->write('Test contend');
    $w->close();
  }

  /**
   * Должен выбрасывать исключение если вызываемого каталога нет в файловой системе.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::clear
   */
  public function testShouldThrowExceptionIfDirNotExists3(){
    $this->setExpectedException('PPHP\tools\classes\standard\fileSystem\NotExistsException');
    $dir = new Directory('PPHP/tests/tools/classes/standard/fileSystem/notExistsDir');
    $dir->clear();
  }

  /**
   * Должен возвращать суммарный размер всех файлов в данном каталоге.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::getSize
   */
  public function testShouldReturnAllSize(){
    $this->assertEquals(12, $this->object->getSize());
  }

  /**
   * Должен возвращать 0 если в каталоге нет ни одного файла.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::getSize
   */
  public function testShouldReturnZeroIfDirEmpty(){
    $assDir = new Directory('PPHP/tests/tools/classes/standard/fileSystem/assistant');
    $this->assertEquals(0, $assDir->getSize());
  }

  /**
   * Должен создавать каталог с указанной маской доступа.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::create
   */
  public function testShouldCreateDir(){
    $dir = new Directory('PPHP/tests/tools/classes/standard/fileSystem/notExistsDir');
    $dir->create();
    $this->assertTrue((file_exists('notExistsDir') && is_dir('notExistsDir')));

    $dir->delete();
  }

  /**
   * Должен предотавращать дублирование.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::create
   */
  public function testShouldPreventDuplicationOfCreate(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $this->object->create();
  }

  /**
   * Должен создавать вложенный каталог с указанной маской доступа.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::createDir
   */
  public function testShouldCreateChildDir(){
    $c = $this->object->createDir('notExistsDir');
    $this->assertTrue((file_exists('dir/notExistsDir') && is_dir('dir/notExistsDir')));

    $c->delete();
  }

  /**
   * Должен предотавращать дублирование.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::createDir
   */
  public function testShouldPreventDuplicationOfCreate2(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $this->object->createDir('dir');
  }

  /**
   * Должен создавать вложенный файл с указанной маской доступа.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::createFile
   */
  public function testShouldCreateChildFile(){
    $c = $this->object->createFile('notExistsFile');
    $this->assertTrue((file_exists('dir/notExistsFile') && is_file('dir/notExistsFile')));

    $c->delete();
  }

  /**
   * Должен предотавращать дублирование.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::createFile
   */
  public function testShouldPreventDuplicationOfCreate3(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $this->object->createFile('fileChild');
  }

  /**
   * Должен возвращать файловый итератор.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::getDirectoryIterator
   */
  public function testShouldReturnFileIterator(){
    $this->assertInstanceOf('DirectoryIterator', $this->object->getDirectoryIterator());
  }

  /**
   * Должен возвращать представление файла в данном каталоге.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::getFile
   */
  public function testShouldReturnFile(){
    $this->assertInstanceOf('PPHP\tools\classes\standard\fileSystem\File', $this->object->getFile('fileChild'));
  }

  /**
   * Должен выбрасывать исключение если требуемого файла нет в вызываемом каталоге.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::getFile
   */
  public function testShouldThrowExceptionIfChildFileNotExists(){
    $this->setExpectedException('PPHP\tools\classes\standard\fileSystem\NotExistsException');
    $this->object->getFile('notExistsFile');
  }

  /**
   * Должен возвращать представление каталога в данном каталоге.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::getDir
   */
  public function testShouldReturnDir(){
    $this->assertInstanceOf('PPHP\tools\classes\standard\fileSystem\Directory', $this->object->getDir('dir'));
  }

  /**
   * Должен выбрасывать исключение если требуемого каталога нет в вызываемом каталоге.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::getDir
   */
  public function testShouldThrowExceptionIfChildDirNotExists(){
    $this->setExpectedException('PPHP\tools\classes\standard\fileSystem\NotExistsException');
    $this->object->getDir('notExistsDir');
  }

  /**
   * Должен возвращать массив имен компонентов в данном каталоге согласно маске поиска.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::getNamesComponents
   */
  public function testShouldReturnArrayChild(){
    $this->assertTrue(is_array($this->object->getNamesComponents()));
  }

  /**
   * Должен возвращать true если запрашиваемый файл существует в вызываемом каталоге, иначе - false.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::isFileExists
   */
  public function testShouldSeekFile(){
    $this->assertTrue($this->object->isFileExists('fileChild'));
    $this->assertFalse($this->object->isFileExists('notExistsFile'));
  }

  /**
   * Должен возвращать true если запрашиваемый каталог существует в вызываемом каталоге, иначе - false.
   * @covers PPHP\tools\classes\standard\fileSystem\Directory::isDirExists
   */
  public function testShouldSeekDir(){
    $this->assertTrue($this->object->isDirExists('dir'));
    $this->assertFalse($this->object->isDirExists('notExistsDir'));
  }
}
