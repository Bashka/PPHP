<?php
namespace PPHP\tests\tools\patterns\cache;

use \PPHP\tests\tools\patterns\cache\CacheMock;
use PPHP\tools\patterns\cache\Cache;

require __DIR__.'/../../../autoload.php';
class CacheTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var CacheMock
   */
  protected $object;

  protected function setUp(){
    $this->object = new CacheMock();
  }

  /**
   * @covers \PPHP\tools\patterns\cache\Cache::getData
   */
  public function testGetData(){
    $this->assertEquals('testKey', $this->object->getData('testKey'));
  }

  /**
   * Проверка механизма удаления устаревших данных.
   * @covers \PPHP\tools\patterns\cache\Cache::getData
   */
  public function testGetDataForFullCache(){
    $this->object->setVolume(2);
    $this->object->getData('1');
    $this->object->getData('2');
    $this->object->getData('3');
    $this->assertEquals(2, $this->object->getDensity());
  }

  /**
   * Проверка не кэширующего кэша.
   * @covers \PPHP\tools\patterns\cache\Cache::getData
   */
  public function testGetDataForOffCache(){
    $this->object->setVolume(Cache::OFF);
    $this->object->getData('1');
    $this->object->getData('2');
    $this->object->getData('3');
    $this->assertEquals(0, $this->object->getDensity());
  }

  /**
   * Проверка неограниченного кэша.
   * @covers \PPHP\tools\patterns\cache\Cache::getData
   */
  public function testGetDataForNoLimitCache(){
    $this->object->setVolume(2);
    $this->object->getData('1');
    $this->object->getData('2');
    $this->object->setVolume(Cache::NO_LIMITED);
    $this->object->getData('3');
    $this->assertEquals(3, $this->object->getDensity());
  }

  /**
   * @covers \PPHP\tools\patterns\cache\Cache::__construct
   */
  public function test_construct(){
    $this->assertEquals(50, $this->object->getVolume());
  }

  /**
   * @covers \PPHP\tools\patterns\cache\Cache::setVolume
   * @covers \PPHP\tools\patterns\cache\Cache::getVolume
   */
  public function testSetVolumeGetVolume(){
    $this->object->setVolume(20);
    $this->assertEquals(20, $this->object->getVolume());
  }

  /**
   * @covers \PPHP\tools\patterns\cache\Cache::getDensity
   */
  public function testGetVolume(){
    $this->assertEquals(0, $this->object->getDensity());

    $this->object->getData('testKey');
    $this->assertEquals(1, $this->object->getDensity());
  }
}