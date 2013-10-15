<?php
namespace PPHP\tests\tools\patterns\metadata;

use PPHP\tools\patterns\metadata\TDescribed;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class TDescribedTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var DescribedMock
   */
  protected $object;

  protected function setUp(){
    $this->object = new DescribedMock;
  }

  /**
   * Должен устанавливать метаданные объекту.
   * @covers PPHP\tools\patterns\metadata\TDescribed::setMetadata
   */
  public function testShouldAddMetadata(){
    $this->object->setMetadata('var', 'val');
    $this->assertEquals('val', $this->object->getMetadata('var'));
  }

  /**
   * В качестве имени метаданных может быть только не пустая строка.
   * @covers PPHP\tools\patterns\metadata\TDescribed::setMetadata
   */
  public function testMetadataNameShouldBeString(){
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    $this->object->setMetadata(1, 'val');
  }

  /**
   * В качестве значения метаданных может выступать пустая строка.
   * @covers PPHP\tools\patterns\metadata\TDescribed::setMetadata
   */
  public function testMetadataValueCanBeStringOrEmptyString(){
    $this->object->setMetadata('var', '');
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    $this->object->setMetadata('var', 1);
  }

  /**
   * Должен возвращать значение установленных метаданных.
   * @covers PPHP\tools\patterns\metadata\TDescribed::getMetadata
   */
  public function testShouldReturnMetadataValue(){
    $this->object->setMetadata('var', 'val');
    $this->assertEquals('val', $this->object->getMetadata('var'));
  }

  /**
   * Должен возвращать null если запрашиваемые метаданные не установлены.
   * @covers PPHP\tools\patterns\metadata\TDescribed::getMetadata
   */
  public function testShouldReturnNullIfMetadataNotAdd(){
    $this->assertEquals(null, $this->object->getMetadata('var'));
  }

  /**
   * Должен возвращать массив всех установленных метаданных.
   * @covers PPHP\tools\patterns\metadata\TDescribed::getAllMetadata
   */
  public function testShouldReturnMetadataArray(){
    $this->object->setMetadata('var', 'val');
    $this->assertEquals('val', $this->object->getAllMetadata()['var']);
  }

  /**
   * Должен возвращать true - если метаданные установлены, иначе - false.
   * @covers PPHP\tools\patterns\metadata\TDescribed::isMetadataExists
   */
  public function testShouldReturnTrueIfMetadataAdd(){
    $this->assertFalse($this->object->isMetadataExists('var'));
    $this->object->setMetadata('var', 'val');
    $this->assertTrue($this->object->isMetadataExists('var'));
  }

  /**
   * Должен удалять метаданные объекта.
   * @covers PPHP\tools\patterns\metadata\TDescribed::removeMetadata
   */
  public function testShouldRemoveMetadata(){
    $this->object->setMetadata('var', 'val');
    $this->object->removeMetadata('var');
    $this->assertFalse($this->object->isMetadataExists('var'));
  }

  /**
   * Если метаданные не установлены, должен ничего не делать.
   * @covers PPHP\tools\patterns\metadata\TDescribed::removeMetadata
   */
  public function testShouldSilentIfMetadataNotAdd(){
    $this->object->removeMetadata('var');
  }
}
