<?php
namespace PPHP\tests\tools\classes\standard\baseType\special\fileSystem;
use PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemAddress;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class FileSystemAddressTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers FileSystemAddress::isReestablish
   */
  public function testIsReestablish(){
    $this->assertFalse(FileSystemAddress::isReestablish(''));
    $this->assertTrue(FileSystemAddress::isReestablish('a'));
    $this->assertTrue(FileSystemAddress::isReestablish('a/b'));
    $this->assertTrue(FileSystemAddress::isReestablish('a/b/c/'));
    $this->assertTrue(FileSystemAddress::isReestablish('/a'));
    $this->assertFalse(FileSystemAddress::isReestablish('a//b'));
    $this->assertFalse(FileSystemAddress::isReestablish('a///b'));
    $this->assertFalse(FileSystemAddress::isReestablish('a//'));
    $this->assertFalse(FileSystemAddress::isReestablish('//a'));
    $this->assertFalse(FileSystemAddress::isReestablish('*'));
    $this->assertFalse(FileSystemAddress::isReestablish(':'));
    $this->assertFalse(FileSystemAddress::isReestablish('?'));
    $this->assertFalse(FileSystemAddress::isReestablish('"'));
    $this->assertFalse(FileSystemAddress::isReestablish('<'));
    $this->assertFalse(FileSystemAddress::isReestablish('>'));
    $this->assertFalse(FileSystemAddress::isReestablish('|'));
    $this->assertFalse(FileSystemAddress::isReestablish("\0"));
    $this->assertFalse(FileSystemAddress::isReestablish('\\'));
  }

  /**
   * @covers FileSystemAddress::reestablish
   * @covers FileSystemAddress::isRoot
   */
  public function testReestablish(){
    $o = FileSystemAddress::reestablish('a/b/c/');
    $this->assertEquals('a/b/c/', $o->getVal());
    $this->assertEquals(false, $o->isRoot());

    $o = FileSystemAddress::reestablish('/a/b/c');
    $this->assertEquals(true, $o->isRoot());
  }
}
