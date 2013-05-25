<?php
namespace PPHP\tests\tools\classes\standard\fileSystem;
use PPHP\tools\classes\standard\fileSystem as fileSystem;

spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});

class RootDirectoryTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var fileSystem\RootDirectory
   */
  protected $object;

  protected function setUp(){
    $this->object = new fileSystem\RootDirectory();
  }

  /**
   * @covers fileSystem\RootDirectory::getLocationAddress
   */
  public function testGetLocationAddress(){
    $this->assertEquals($_SERVER['DOCUMENT_ROOT'], $this->object->getLocationAddress());
  }

  /**
   * @covers fileSystem\RootDirectory::getAddress
   */
  public function testGetAddress(){
    $this->assertEquals($_SERVER['DOCUMENT_ROOT'], $this->object->getLocationAddress());
  }

  /**
   * @covers fileSystem\RootDirectory::rename
   */
  public function testRename(){
    $this->setExpectedException('\PPHP\tools\classes\standard\fileSystem\UpdatingRoodException');
    $this->object->rename('NewName');
  }

  /**
   * @covers fileSystem\RootDirectory::move
   */
  public function testMove(){
    $this->setExpectedException('\PPHP\tools\classes\standard\fileSystem\UpdatingRoodException');
    $this->object->move($this->object);
  }

  /**
   * @covers fileSystem\RootDirectory::isExists
   */
  public function testIsExists(){
    $this->assertTrue($this->object->isExists());
  }

  /**
   * @covers fileSystem\RootDirectory::copyPaste
   */
  public function testCopyPaste(){
    $this->setExpectedException('\PPHP\tools\classes\standard\fileSystem\UpdatingRoodException');
    $this->object->copyPaste($this->object);
  }

  /**
   * @covers fileSystem\RootDirectory::getSize
   */
  public function testGetSize(){
    $this->assertEquals(0, $this->object->getSize());
  }

  /**
   * @covers fileSystem\RootDirectory::create
   */
  public function testCreate(){
    $this->setExpectedException('\PPHP\tools\classes\standard\fileSystem\UpdatingRoodException');
    $this->object->create();
  }
}
