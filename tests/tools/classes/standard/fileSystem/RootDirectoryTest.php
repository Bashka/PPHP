<?php
namespace PPHP\tests\tools\classes\standard\fileSystem;

use PPHP\tools\classes\standard\fileSystem as fileSystem;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')).'PPHP/dev/autoload/autoload.php';

class RootDirectoryTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var fileSystem\RootDirectory
   */
  protected $object;

  protected function setUp(){
    $this->object = new fileSystem\RootDirectory();
  }

  /**
   * Должен возвращать домен ресурса.
   * @covers PPHP\tools\classes\standard\fileSystem\RootDirectory::getLocationAddress
   */
  public function testShouldReturnDomainName(){
    $this->assertEquals($_SERVER['DOCUMENT_ROOT'], $this->object->getLocationAddress());
  }

  /**
   * Должен возвращать домен ресурса.
   * @covers PPHP\tools\classes\standard\fileSystem\RootDirectory::getAddress
   */
  public function testShouldReturnDomainName2(){
    $this->assertEquals($_SERVER['DOCUMENT_ROOT'], $this->object->getLocationAddress());
  }

  /**
   * Должен выбрасывать исключение при вызове.
   * @covers PPHP\tools\classes\standard\fileSystem\RootDirectory::rename
   */
  public function testShouldThrowException(){
    $this->setExpectedException('\PPHP\tools\classes\standard\fileSystem\UpdatingRoodException');
    $this->object->rename('NewName');
  }

  /**
   * Должен выбрасывать исключение при вызове.
   * @covers PPHP\tools\classes\standard\fileSystem\RootDirectory::move
   */
  public function testShouldThrowException2(){
    $this->setExpectedException('\PPHP\tools\classes\standard\fileSystem\UpdatingRoodException');
    $this->object->move($this->object);
  }

  /**
   * Должен вернуть true.
   * @covers PPHP\tools\classes\standard\fileSystem\RootDirectory::isExists
   */
  public function testShouldReturnTrue(){
    $this->assertTrue($this->object->isExists());
  }

  /**
   * Должен выбрасывать исключение при вызове.
   * @covers PPHP\tools\classes\standard\fileSystem\RootDirectory::copyPaste
   */
  public function testShouldThrowException3(){
    $this->setExpectedException('\PPHP\tools\classes\standard\fileSystem\UpdatingRoodException');
    $this->object->copyPaste($this->object);
  }

  /**
   * Должен вернуть 0.
   * @covers PPHP\tools\classes\standard\fileSystem\RootDirectory::getSize
   */
  public function testShouldReturnZero(){
    $this->assertEquals(0, $this->object->getSize());
  }

  /**
   * Должен выбрасывать исключение при вызове.
   * @covers PPHP\tools\classes\standard\fileSystem\RootDirectory::create
   */
  public function testShouldThrowException4(){
    $this->setExpectedException('\PPHP\tools\classes\standard\fileSystem\UpdatingRoodException');
    $this->object->create();
  }
}
