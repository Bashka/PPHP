<?php
namespace PPHP\tests\tools\classes\standard\fileSystem;

use PPHP\tools\classes\standard\fileSystem\File;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')).'PPHP/dev/autoload/autoload.php';

class FileTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var File
   */
  private $object;

  protected function setUp(){
    $this->object = new File('PPHP/tests/tools/classes/standard/fileSystem/file');
  }

  /**
   * Компонента с данным именем в родительском каталоге не должно существовать.
   * @covers PPHP\tools\classes\standard\fileSystem\File::rename
   */
  public function testShouldPreventDuplicationOfRename(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $this->object->rename('assistant.txt');
  }

  /**
   * В аргументе не должно присутствовать символа слеша.
   * @covers PPHP\tools\classes\standard\fileSystem\File::rename
   */
  public function testShouldThrowExceptionIfNewNameIsAddress(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    $this->object->rename('dir/assistant');
  }

  /**
   * Должен выполнять копирование файла вместе с содержимым.
   * @covers PPHP\tools\classes\standard\fileSystem\File::copyPaste
   */
  public function testShouldCopyFile(){
    $dir = $this->object->getLocation()->getDir('assistant');
    $copy = $this->object->copyPaste($dir);
    $newAddress = $_SERVER['DOCUMENT_ROOT'].'/PPHP/tests/tools/classes/standard/fileSystem/assistant/file';
    $this->assertTrue(file_exists($newAddress) && is_file($newAddress));
    $this->assertEquals('', file_get_contents($newAddress));
    $copy->delete();
  }

  /**
   * Должен возвращать представление созданной копии.
   * @covers PPHP\tools\classes\standard\fileSystem\File::copyPaste
   */
  public function testShouldReturnCopy(){
    $dir = $this->object->getLocation()->getDir('assistant');
    $copy = $this->object->copyPaste($dir);
    $this->assertInstanceOf('PPHP\tools\classes\standard\fileSystem\File', $copy);
    $copy->delete();
  }

  /**
   * Должен выбрасывать исключение если вызываемого файла нет в файловой системе.
   * @covers PPHP\tools\classes\standard\fileSystem\File::copyPaste
   */
  public function testShouldThrowExceptionIfFileNotExists(){
    $this->setExpectedException('PPHP\tools\classes\standard\fileSystem\NotExistsException');
    $file = new File('PPHP/tests/tools/classes/standard/fileSystem/notExistsFile');
    $file->copyPaste($this->object->getLocation());
  }

  /**
   * Должен предотвращать дублирование.
   * @covers PPHP\tools\classes\standard\fileSystem\File::copyPaste
   */
  public function testShouldPreventDuplicationOfCopy(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $this->object->copyPaste($this->object->getLocation());
  }

  /**
   * Должен возвращать размер файла.
   * @covers PPHP\tools\classes\standard\fileSystem\File::getSize
   */
  public function testShouldReturnAllSize(){
    $w = $this->object->getWriter();
    $w->write('Hello');
    $this->assertEquals(5, $this->object->getSize());
    $w->clean();
    $this->assertEquals(0, $this->object->getSize());
    $w->close();
  }

  /**
   * Должен вырасывать исключение, если вызываемый файл отсутствует в файловой системе.
   * @covers PPHP\tools\classes\standard\fileSystem\File::getSize
   */
  public function testShouldThrowExceptionIfFileNotExists2(){
    $this->setExpectedException('PPHP\tools\classes\standard\fileSystem\NotExistsException');
    $file = new File('PPHP/tests/tools/classes/standard/fileSystem/notExistsFile');
    $file->getSize();
  }

  /**
   * Должен возвращать true если вызываемый файл существует в файловой системе, иначе - false.
   * @covers PPHP\tools\classes\standard\fileSystem\File::isExists
   */
  public function testShouldReturnTrueIfFileExists(){
    $this->assertTrue($this->object->isExists());
    $file = new File('PPHP/tests/tools/classes/standard/fileSystem/notExistsFile');
    $this->assertFalse($file->isExists());
  }

  /**
   * Должен выбрасывать исключение если родительского каталога не существует.
   * @covers PPHP\tools\classes\standard\fileSystem\File::isExists
   */
  public function testShouldThrowExceptionIfNotExistsParentDir(){
    $this->setExpectedException('PPHP\tools\classes\standard\fileSystem\NotExistsException');
    $file = new File('PPHP/tests/tools/classes/standard/fileSystem/notExistsDir/notExistsFile');
    $file->isExists();
  }

  /**
   * Должен создавать файл с указанной маской доступа.
   * @covers PPHP\tools\classes\standard\fileSystem\File::create
   */
  public function testShouldCreateFile(){
    $file = new File('PPHP/tests/tools/classes/standard/fileSystem/assistant/file');
    $file->create();
    $newAddress = $_SERVER['DOCUMENT_ROOT'].'/PPHP/tests/tools/classes/standard/fileSystem/assistant/file';
    $this->assertTrue(file_exists($newAddress) && is_file($newAddress));
    $file->delete();
  }

  /**
   * Должен предотавращать дублирование.
   * @covers PPHP\tools\classes\standard\fileSystem\File::create
   */
  public function testShouldPreventDuplicationOfCreate(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\DuplicationException');
    $file = new File('PPHP/tests/tools/classes/standard/fileSystem/file');
    $file->create();
  }

  /**
   * Должен удалять файл.
   * @covers PPHP\tools\classes\standard\fileSystem\File::delete
   */
  public function testShouldRemoveFile(){
    $this->object->delete();
    $address = $_SERVER['DOCUMENT_ROOT'].'/PPHP/tests/tools/classes/standard/fileSystem/file';
    $this->assertTrue(!file_exists($address) || !is_file($address));
    $this->object->create();
  }

  /**
   * Должен выбрасывать исключение если вызываемого файла нет в файловой системе.
   * @covers PPHP\tools\classes\standard\fileSystem\File::delete
   */
  public function testShouldThrowExceptionIfFileNotExists3(){
    $this->setExpectedException('PPHP\tools\classes\standard\fileSystem\NotExistsException');
    $file = new File('PPHP/tests/tools/classes/standard/fileSystem/notExistsFile');
    $file->delete();
  }

  /**
   * Должен возвращать расширение файла.
   * @covers PPHP\tools\classes\standard\fileSystem\File::getType
   */
  public function testShouldReturnFileType(){
    $file = new File('PPHP/tests/tools/classes/standard/fileSystem/assistant.txt');
    $this->assertEquals('txt', $file->getType());
  }

  /**
   * Должен возвращать пустую строку, если у файла нет расширения.
   * @covers PPHP\tools\classes\standard\fileSystem\File::getType
   */
  public function testShouldReturnEmptyStringIfFileNotType(){
    $this->assertEquals('', $this->object->getType());
  }

  /**
   * Должен возвращать файловый поток ввода.
   * @covers PPHP\tools\classes\standard\fileSystem\File::getReader
   */
  public function testShouldReturnFileInStream(){
    $r = $this->object->getReader();
    $this->assertInstanceOf('PPHP\tools\classes\standard\fileSystem\io\BlockingFileReader', $r);
    $r->close();
  }

  /**
   * Должен возвращать файловый поток вывода.
   * @covers PPHP\tools\classes\standard\fileSystem\File::getWriter
   */
  public function testShouldReturnFileOutStream(){
    $r = $this->object->getWriter();
    $this->assertInstanceOf('PPHP\tools\classes\standard\fileSystem\io\BlockingFileWriter', $r);
    $r->close();
  }
}
