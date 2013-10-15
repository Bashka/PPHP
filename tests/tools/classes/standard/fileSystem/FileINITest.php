<?php
namespace PPHP\tests\tools\classes\standard\fileSystem;

use PPHP\tools\classes\standard\fileSystem\File;
use PPHP\tools\classes\standard\fileSystem\FileINI;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')).'PPHP/dev/autoload/autoload.php';

class FileINITest extends \PHPUnit_Framework_TestCase{
  /**
   * @var FileINI
   */
  private $ini;

  /**
   * @var FileINI
   */
  private $section;

  protected function setUp(){
    $this->ini = new FileINI(new File('PPHP/tests/tools/classes/standard/fileSystem/file.ini'));
    $this->section = new FileINI(new File('PPHP/tests/tools/classes/standard/fileSystem/section.ini'), true);
  }

  /**
   * Должен выбрасывать исключение в случае отсутствия целевого файла.
   * @covers PPHP\tools\classes\standard\fileSystem\FileINI::__construct
   */
  public function testShouldThrowExceptionIfFileNotExists(){
    $this->setExpectedException('PPHP\tools\classes\standard\fileSystem\NotExistsException');
    new FileINI(new File('PPHP/tests/tools/classes/standard/fileSystem/notExistsFile'));
  }

  /**
   * Должен возвращать запрашиваемое значение.
   * @covers PPHP\tools\classes\standard\fileSystem\FileINI::get
   */
  public function testShouldReturnValue(){
    $this->assertEquals('ivan', $this->ini->get('name'));
  }

  /**
   * Должен возвращать запрашиваемое значение из указанной секции.
   * @covers PPHP\tools\classes\standard\fileSystem\FileINI::get
   */
  public function testShouldReturnSectionValue(){
    $this->assertEquals('ivan', $this->section->get('name', 'user'));
  }

  /**
   * Должен возвращать null если запрашиваемое значение отсутствует.
   * @covers PPHP\tools\classes\standard\fileSystem\FileINI::get
   */
  public function testShouldReturnNullIfValueNotExists(){
    $this->assertEquals(null, $this->ini->get('key'));
    $this->assertEquals(null, $this->section->get('key', 'user'));
  }

  /**
   * Должен возвращать содержимое указанной секции в виде ассоциативного массива.
   * @covers PPHP\tools\classes\standard\fileSystem\FileINI::getSection
   */
  public function testShouldReturnSection(){
    $this->assertEquals(['name' => 'ivan', 'age' => '18'], $this->section->getSection('user'));
  }

  /**
   * Должен возвращать пустой массив, если запрашиваемой секции нет в файле или если файл не разделен на секции.
   * @covers PPHP\tools\classes\standard\fileSystem\FileINI::getSection
   */
  public function testShouldReturnEmptyArrayIfSectionNotFound(){
    $this->assertEquals([], $this->section->getSection('section'));
  }

  /**
   * Должен устанавливать значение ключу без изменения файла.
   * @covers PPHP\tools\classes\standard\fileSystem\FileINI::set
   */
  public function testShouldSetValue(){
    $this->ini->set('name', 'petr');
    $this->assertEquals('petr', $this->ini->get('name'));
    $this->assertEquals('name=ivan'."\n".'age=18', file_get_contents($_SERVER['DOCUMENT_ROOT'].'/PPHP/tests/tools/classes/standard/fileSystem/file.ini'));
    $this->ini->set('name', 'ivan');
  }

  /**
   * Должен удалять указанный ключ и его значение без изменения файла.
   * @covers PPHP\tools\classes\standard\fileSystem\FileINI::remove
   */
  public function testShouldRemoveValue(){
    $this->ini->remove('name');
    $this->assertFalse($this->ini->isDataExists('name'));
    $this->assertEquals('name=ivan'."\n".'age=18', file_get_contents($_SERVER['DOCUMENT_ROOT'].'/PPHP/tests/tools/classes/standard/fileSystem/file.ini'));
    $this->ini->set('name', 'ivan');
  }

  /**
   * Должен записывать все изменения в файл.
   * @covers PPHP\tools\classes\standard\fileSystem\FileINI::rewrite
   */
  public function testShouldWriteFile(){
    $this->ini->set('name', 'petr');
    $this->ini->remove('age');
    $this->ini->rewrite();
    $file = $_SERVER['DOCUMENT_ROOT'].'/PPHP/tests/tools/classes/standard/fileSystem/file.ini';
    $this->assertEquals('name=petr'."\n", file_get_contents($file));
    file_put_contents($file, 'name=ivan'."\n".'age=18');

  }

  /**
   * Должен возвращать true - если в файле имеется указанная секция и false - если секция отсутствует или файл не разделен на секции.
   * @covers PPHP\tools\classes\standard\fileSystem\FileINI::isSectionExists
   */
  public function testShouldReturnTrueIfSectionExists(){
    $this->assertTrue($this->section->isSectionExists('user'));
    $this->assertFalse($this->section->isSectionExists('section'));
  }

  /**
   * Должен возвращать true - если в файле имеется указанный ключ.
   * @covers PPHP\tools\classes\standard\fileSystem\FileINI::isDataExists
   */
  public function testShouldReturnTrueIfKeyExists(){
    $this->assertTrue($this->ini->isDataExists('name'));
    $this->assertFalse($this->ini->isDataExists('key'));
  }

  /**
   * Должен возвращать значение ключа при вызове вида: имяКлюча.
   * @covers PPHP\tools\classes\standard\fileSystem\FileINI::__get
   */
  public function testShouldReturnValue2(){
    $this->assertEquals('ivan', $this->ini->name);
  }

  /**
   * Должен возвращать значение ключа в секции при вызове вида: имяСекции_имяКлюча.
   * @covers PPHP\tools\classes\standard\fileSystem\FileINI::__get
   */
  public function testShouldReturnSectionValue2(){
    $this->assertEquals('ivan', $this->section->user_name);
  }

  /**
   * Должен устанавливать значение ключа при вызове вида: имяКлюча.
   * @covers PPHP\tools\classes\standard\fileSystem\FileINI::__set
   */
  public function testShouldSetValue2(){
    $this->ini->name = 'petr';
    $this->assertEquals('petr', $this->ini->name);
    $this->ini->name = 'ivan';
  }

  /**
   * Должен устанавливать значение ключа в секции при вызове вида: имяСекции_имяКлюча.
   * @covers PPHP\tools\classes\standard\fileSystem\FileINI::__set
   */
  public function testShouldSetSectionValue(){
    $this->section->user_name = 'petr';
    $this->assertEquals('petr', $this->section->user_name);
    $this->section->user_name = 'ivan';
  }

  /**
   * Должен определять наличие ключа при вызове вида: имяКлюча.
   * @covers PPHP\tools\classes\standard\fileSystem\FileINI::__isset
   */
  public function testShouldReturnTrueIfKeyExists2(){
    $this->assertTrue(isset($this->ini->name));
    $this->assertFalse(isset($this->ini->key));
  }

  /**
   * Должен определять наличие ключа в секции при вызове вида: имяСекции_имяКлюча.
   * @covers PPHP\tools\classes\standard\fileSystem\FileINI::__isset
   */
  public function testShouldReturnTrueIfSectionKeyExists(){
    $this->assertTrue(isset($this->section->user_name));
    $this->assertFalse(isset($this->section->user_key));
  }

  /**
   * Должен удалять ключ при вызове вида: имяКлюча.
   * @covers PPHP\tools\classes\standard\fileSystem\FileINI::__unset
   */
  public function testShouldRemoveKey(){
    unset($this->ini->name);
    $this->assertFalse(isset($this->ini->name));
    $this->ini->name = 'ivan';
  }

  /**
   * Должен удалять ключ в секции при вызове вида: имяСекции_имяКлюча.
   * @covers PPHP\tools\classes\standard\fileSystem\FileINI::__unset
   */
  public function testShouldRemoveSectionKey(){
    unset($this->section->user_name);
    $this->assertFalse(isset($this->section->user_name));
    $this->section->user_name = 'ivan';
  }
}

