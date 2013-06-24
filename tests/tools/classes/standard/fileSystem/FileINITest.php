<?php
namespace PPHP\tests\tools\classes\standard\fileSystem;

use PPHP\tools\classes\standard\fileSystem as fileSystem;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class FileINITest extends \PHPUnit_Framework_TestCase{
  /**
   * @var fileSystem\FileINI
   */
  protected $object;

  /**
   * @var fileSystem\FileINI
   */
  protected $objectSection;

  /**
   * @var fileSystem\File
   */
  static protected $file;

  /**
   * @var fileSystem\File
   */
  static protected $fileSection;

  /**
   * Имя тестируемого файла в текущем каталоге без секций.
   */
  const testFileName = 'testFile.ini';

  /**
   * Имя тестируемого файла в текущем каталоге с секциями.
   */
  const testSectionFileName = 'testFileSection.ini';

  public static function setUpBeforeClass(){
    parent::setUpBeforeClass();
    fclose(fopen(self::testFileName, 'a+'));
    self::$file = fileSystem\ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/tests/tools/classes/standard/fileSystem/' . self::testFileName);
    fclose(fopen(self::testSectionFileName, 'a+'));
    self::$fileSection = fileSystem\ComponentFileSystem::constructFileFromAddress($_SERVER['DOCUMENT_ROOT'] . '/PPHP/tests/tools/classes/standard/fileSystem/' . self::testSectionFileName);
  }

  public static function tearDownAfterClass(){
    parent::tearDownAfterClass();
    unlink(self::testFileName);
    unlink(self::testSectionFileName);
  }

  protected function setUp(){
    $this->object = new fileSystem\FileINI(self::$file);
    $this->objectSection = new fileSystem\FileINI(self::$fileSection, true);
    $d = fopen(self::testFileName, 'w+');
    fwrite($d, "k1=v1\nk2=v2\nk3=");
    fclose($d);
    $d = fopen(self::testSectionFileName, 'w+');
    fwrite($d, "[section]\nk1=v1\nk2=v2\nk3=");
    fclose($d);
  }

  /**
   * @covers fileSystem\FileINI::get
   */
  public function testGet(){
    $this->assertEquals('v1', $this->object->get('k1'));
    $this->assertEquals('', $this->object->get('k3'));
    $this->assertEquals('v1', $this->objectSection->get('k1', 'section'));
    $this->assertEquals('', $this->objectSection->get('k3', 'section'));
  }

  /**
   * @covers fileSystem\FileINI::set
   */
  public function testSet(){
    $this->object->set('k1', 'newV1');
    $this->object->set('k3', 'v3');
    $this->assertEquals('newV1', $this->object->get('k1'));
    $this->assertEquals('v3', $this->object->get('k3'));
    $this->objectSection->set('k1', 'newV1', 'section');
    $this->objectSection->set('k3', 'v3', 'section');
    $this->assertEquals('newV1', $this->objectSection->get('k1', 'section'));
    $this->assertEquals('v3', $this->objectSection->get('k3', 'section'));
  }

  /**
   * @covers fileSystem\FileINI::remove
   */
  public function testRemove(){
    $this->object->remove('k1');
    $this->assertEquals(null, $this->object->get('k1'));
    $this->objectSection->remove('k1', 'section');
    $this->assertEquals(null, $this->objectSection->get('k1', 'section'));
  }

  /**
   * @covers fileSystem\FileINI::rewrite
   */
  public function testRewrite(){
    $this->object->set('k1', 'newV1');
    $this->object->set('k3', 'v3');
    $this->objectSection->set('k1', 'newV1', 'section');
    $this->objectSection->set('k3', 'v3', 'section2');
    $this->object->rewrite();
    $this->objectSection->rewrite();
    $this->assertEquals("k1=newV1\nk2=v2\nk3=v3\n", file_get_contents(self::testFileName));
    $this->assertEquals("[section]\nk1=newV1\nk2=v2\nk3=\n[section2]\nk3=v3\n", file_get_contents(self::testSectionFileName));
    $this->object->remove('k1');
    $this->object->rewrite();
    $this->assertEquals("k2=v2\nk3=v3\n", file_get_contents(self::testFileName));
  }

  /**
   * @covers fileSystem\FileINI::isSectionExists
   */
  public function testIsSectionExists(){
    $this->assertFalse($this->objectSection->isSectionExists('section3'));
    $this->assertTrue($this->objectSection->isSectionExists('section'));
  }

  /**
   * @covers fileSystem\FileINI::isDataExists
   */
  public function testIsDataExists(){
    $this->assertFalse($this->objectSection->isDataExists('k4', 'section'));
    $this->assertTrue($this->objectSection->isDataExists('k1', 'section'));
    $this->assertTrue($this->objectSection->isDataExists('k3', 'section'));
    $this->assertFalse($this->object->isDataExists('k4'));
    $this->assertTrue($this->object->isDataExists('k1'));
    $this->assertTrue($this->object->isDataExists('k3'));
  }

  /**
   * @covers fileSystem\FileINI::__set
   */
  public function test__set(){
    $this->object->k1 = 'newV1';
    $this->object->k3 = 'v3';
    $this->assertEquals('newV1', $this->object->get('k1'));
    $this->assertEquals('v3', $this->object->get('k3'));
    $this->objectSection->section_k1 = 'newV1';
    $this->objectSection->section_k3 = 'v3';
    $this->assertEquals('newV1', $this->objectSection->get('k1', 'section'));
    $this->assertEquals('v3', $this->objectSection->get('k3', 'section'));
  }

  /**
   * @covers fileSystem\FileINI::__get
   */
  public function test__get(){
    $this->assertEquals('v1', $this->object->k1);
    $this->assertEquals('', $this->object->k3);
    $this->assertEquals('v1', $this->objectSection->section_k1);
    $this->assertEquals('', $this->objectSection->section_k3);
  }

  /**
   * @covers fileSystem\FileINI::__isset
   */
  public function test__isset(){
    $this->assertFalse(isset($this->objectSection->section_k4));
    $this->assertTrue(isset($this->objectSection->section_k1));
    $this->assertTrue(isset($this->objectSection->section_k3));
    $this->assertFalse(isset($this->object->k4));
    $this->assertTrue(isset($this->object->k1));
    $this->assertTrue(isset($this->object->k3));
  }

  /**
   * @covers fileSystem\FileINI::remove
   */
  public function test__unset(){
    unset($this->object->k1);
    $this->assertEquals(null, $this->object->get('k1'));
    unset($this->objectSection->section_k1);
    $this->assertEquals(null, $this->objectSection->get('k1', 'section'));
  }
}

