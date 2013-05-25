<?php
namespace PPHP\tests\tools\classes\standard\baseType\special\fileSystem;
use PPHP\tools\classes\standard\baseType\special\fileSystem\FileSystemName;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class FileSystemNameTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers FileSystemName::reestablish
   */
  public function testReestablish(){
    $o = FileSystemName::reestablish('test.Name');
    $this->assertEquals('test', $o->getName());
    $this->assertEquals('Name', $o->getExpansion());

    $o = FileSystemName::reestablish('test');
    $this->assertEquals('test', $o->getName());
  }

  /**
   * @covers FileSystemName::isReestablish
   */
  public function testIs(){
    $this->assertFalse(FileSystemName::isReestablish(''));
    $this->assertTrue(FileSystemName::isReestablish('a'));
    $this->assertTrue(FileSystemName::isReestablish('a.b'));
    $this->assertTrue(FileSystemName::isReestablish('1'));
    $this->assertTrue(FileSystemName::isReestablish('1.2'));
    $this->assertTrue(FileSystemName::isReestablish('.a.b'));
    $this->assertTrue(FileSystemName::isReestablish('.a.1'));
    $this->assertTrue(FileSystemName::isReestablish('a_b.a-b'));
    $this->assertFalse(FileSystemName::isReestablish('<.a'));
    $this->assertFalse(FileSystemName::isReestablish('>.a'));
    $this->assertFalse(FileSystemName::isReestablish('|.a'));
    $this->assertFalse(FileSystemName::isReestablish('\\.a'));
    $this->assertFalse(FileSystemName::isReestablish('/.a'));
    $this->assertFalse(FileSystemName::isReestablish(':.a'));
    $this->assertFalse(FileSystemName::isReestablish('?.a'));
    $this->assertFalse(FileSystemName::isReestablish('*.a'));
    $this->assertFalse(FileSystemName::isReestablish('".a'));
    $this->assertFalse(FileSystemName::isReestablish("a.b\0"));
  }
}
