<?php
namespace PPHP\tests\tools\patterns\cache;

use PPHP\tests\tools\patterns\cache\CacheMock;
use PPHP\tools\patterns\cache\Cache;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class CacheTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var CacheMock
   */
  protected $object;

  protected function setUp(){
    CacheMock::$data = ['key' => 1];
    $this->object = new CacheMock;
  }

  /**
   * Должен определять объем кэша в 50 элементов по умолчанию.
   * @covers \PPHP\tools\patterns\cache\Cache::__construct
   */
  public function testDefaultCacheVolume50(){
    $this->assertEquals(50, $this->object->getVolume());
  }

  /**
   * Должен определять объем кэша.
   * @covers \PPHP\tools\patterns\cache\Cache::__construct
   */
  public function testShouldSetCacheVolume(){
    $this->assertEquals(10, (new CacheMock(10))->getVolume());
  }

  /**
   * Должен возвращать null при отсутствии незакэшированных данных в ресурсе.
   * @covers \PPHP\tools\patterns\cache\Cache::getData
   */
  public function testShouldReturnNullForEmptyData(){
    $this->assertEquals(null, $this->object->getData('noKey'));
  }

  /**
   * Должен кэшировать даже отсутствие данных.
   * @covers \PPHP\tools\patterns\cache\Cache::getData
   */
  public function testShouldCachedNull(){
    $this->object->getData('noKey');
    CacheMock::$data = ['noKey' => 1];
    $this->assertEquals(null, $this->object->getData('noKey'));
  }

  /**
   * Для ограниченного кэша должен выполнять кэширование данных.
   * @covers \PPHP\tools\patterns\cache\Cache::getData
   */
  public function testForLimitedCacheShouldCached(){
    $this->assertEquals(1, $this->object->getData('key'));
    CacheMock::$data = ['key' => 2];
    $this->assertEquals(1, $this->object->getData('key'));
  }

  /**
   * Для неограниченного кэша должен выполнять кэширование данных.
   * @covers \PPHP\tools\patterns\cache\Cache::getData
   */
  public function testForNoLimitedCacheShouldCached(){
    $this->object->setVolume(Cache::NO_LIMITED);
    $this->assertEquals(1, $this->object->getData('key'));
    CacheMock::$data = ['key' => 2];
    $this->assertEquals(1, $this->object->getData('key'));
  }

  /**
   * Для отключенного кэша не должен выполнять кэширование данных.
   * @covers \PPHP\tools\patterns\cache\Cache::getData
   */
  public function testForOffCacheNoShouldCached(){
    $this->object->setVolume(Cache::OFF);
    $this->assertEquals(1, $this->object->getData('key'));
    CacheMock::$data = ['key' => 2];
    $this->assertEquals(2, $this->object->getData('key'));
  }

  /**
   * Для ограниченного кэша должен удалять устаревшие данные.
   * @covers \PPHP\tools\patterns\cache\Cache::getData
   */
  public function testForLimitedCacheShouldDeleteOldData(){
    $this->object->setVolume(3);
    CacheMock::$data = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];
    $this->assertEquals(1, $this->object->getData('a'));
    $this->object->getData('b');
    $this->object->getData('c');
    $this->object->getData('d');
    CacheMock::$data = ['a' => 2, 'b' => 2, 'c' => 3, 'd' => 4];
    $this->assertEquals(2, $this->object->getData('a'));
  }

  /**
   * Для неограниченного кэша не должен удалять устаревшие данные.
   * @covers \PPHP\tools\patterns\cache\Cache::getData
   */
  public function testForNoLimitedCacheNoShouldDeleteOldData(){
    $this->object->setVolume(Cache::NO_LIMITED);
    CacheMock::$data = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];
    $this->assertEquals(1, $this->object->getData('a'));
    $this->object->getData('b');
    $this->object->getData('c');
    $this->object->getData('d');
    CacheMock::$data = ['a' => 2, 'b' => 2, 'c' => 3, 'd' => 4];
    $this->assertEquals(1, $this->object->getData('a'));
  }

  /**
   * Для ограниченного кэша должен возвращать его текущий объем.
   * @covers \PPHP\tools\patterns\cache\Cache::getVolume
   */
  public function testForLimitedCacheShouldReturnVolume(){
    $this->assertEquals(50, $this->object->getVolume());
  }

  /**
   * Для неограниченного кэша должен возвращать константу NO_LIMITED.
   * @covers \PPHP\tools\patterns\cache\Cache::getVolume
   */
  public function testForNoLimitedCacheShouldReturnNO_LIMITEDConst(){
    $this->object->setVolume(Cache::NO_LIMITED);
    $this->assertEquals(Cache::NO_LIMITED, $this->object->getVolume());
  }

  /**
   * Для отключенного кэша должен возвращать константу OFF.
   * @covers \PPHP\tools\patterns\cache\Cache::getVolume
   */
  public function testForOffCacheShouldReturnOFFConst(){
    $this->object->setVolume(Cache::OFF);
    $this->assertEquals(Cache::OFF, $this->object->getVolume());
  }

  /**
   * Должен изменять объем кэша динамически.
   * @covers \PPHP\tools\patterns\cache\Cache::setVolume
   */
  public function testShouldChangeVolumeCacheDynamic(){
    $this->object->setVolume(3);
    CacheMock::$data = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];
    $this->assertEquals(1, $this->object->getData('a'));
    $this->object->getData('b');
    $this->object->getData('c');
    $this->object->setVolume(4);
    $this->object->getData('d');
    CacheMock::$data = ['a' => 2, 'b' => 2, 'c' => 3, 'd' => 4];
    $this->assertEquals(1, $this->object->getData('a'));
  }

  /**
   * Должен возвращать ноль, если в кэше нет данных.
   * @covers \PPHP\tools\patterns\cache\Cache::getDensity
   */
  public function testShouldReturnZeroIfCacheEmpty(){
    $this->assertEquals(0, $this->object->getDensity());
  }

  /**
   * Должен возвращать плотность любого типа кэша.
   * @covers \PPHP\tools\patterns\cache\Cache::getDensity
   */
  public function testShouldReturnDensityForAllTypeCache(){
    $this->object->getData('key');
    $this->assertEquals(1, $this->object->getDensity());
    $this->object->setVolume(Cache::NO_LIMITED);
    $this->assertEquals(1, $this->object->getDensity());
    $this->object->setVolume(Cache::OFF);
    $this->assertEquals(1, $this->object->getDensity());
  }

  /**
   * Должен удалять все закэшированные данные для ограниченного кэша.
   * @covers \PPHP\tools\patterns\cache\Cache::clear
   */
  public function testShouldDeleteAllDataForLimitedCache(){
    $this->assertEquals(1, $this->object->getData('key'));
    CacheMock::$data = ['key' => 2];
    $this->object->clear();
    $this->assertEquals(2, $this->object->getData('key'));
  }

  /**
   * Должен удалять все закэшированные данные для неограниченного кэша.
   * @covers \PPHP\tools\patterns\cache\Cache::clear
   */
  public function testShouldDeleteAllDataForNoLimitedCache(){
    $this->object->setVolume(Cache::NO_LIMITED);
    $this->assertEquals(1, $this->object->getData('key'));
    CacheMock::$data = ['key' => 2];
    $this->object->clear();
    $this->assertEquals(2, $this->object->getData('key'));
  }

  /**
   * Должен сбрасывать плотность кэша в ноль.
   * @covers \PPHP\tools\patterns\cache\Cache::clear
   */
  public function testShouldSetCacheDensityToZero(){
    $this->object->getData('key');
    $this->object->clear();
    $this->assertEquals(0, $this->object->getDensity());
  }
}