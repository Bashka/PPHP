<?php
namespace PPHP\tests\tools\patterns\metadata;

use PPHP\tools\patterns\metadata\TDescribed;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class DescribedTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var MetadataMock
   */
  protected $object;

  protected function setUp(){
    $this->object = new MetadataMock;
  }

  /**
   * @covers TDescribed::setMetadata
   * @covers TDescribed::getAllMetadata
   */
  public function testSetMetadata(){
    $this->object->setMetadata('var', 'val');
    $metadata = $this->object->getAllMetadata();
    $this->assertArrayHasKey('var', $metadata);
    $this->assertEquals('val', $metadata['var']);
  }

  /**
   * @covers TDescribed::getMetadata
   */
  public function testGetMetadata(){
    $this->object->setMetadata('var', 'val');
    $this->assertEquals('val', $this->object->getMetadata('var'));
  }

  /**
   * @covers TDescribed::isMetadataExists
   */
  public function testIsMetadataExists(){
    $this->assertFalse($this->object->isMetadataExists('var'));
    $this->object->setMetadata('var', 'val');
    $this->assertTrue($this->object->isMetadataExists('var'));
  }

  /**
   * @covers TDescribed::removeMetadata
   */
  public function testRemoveMetadata(){
    $this->object->setMetadata('var', 'val');
    $this->object->removeMetadata('var');
    $this->assertFalse($this->object->isMetadataExists('var'));
  }
}
