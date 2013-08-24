<?php
namespace PPHP\tests\tools\classes\standard\storage\cache;

use PPHP\tools\classes\standard\baseType\exceptions\RuntimeException;
use PPHP\tools\classes\standard\storage\cache\Cache;
use PPHP\tools\classes\standard\storage\cache\CacheAdapter;
use PPHP\tools\classes\standard\storage\cache\drivers\NullAdapter;
use PPHP\tools\classes\standard\storage\cache\Synchronizer;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class SynchronizerTest extends \PHPUnit_Framework_TestCase{
  /**
   * @var CacheAdapter
   */
  private static $cache;

  /**
   * @var Synchronizer
   */
  private static $synch;

  public static function setUpBeforeClass(){
    parent::setUpBeforeClass();
    self::$cache = Cache::getInstance();
    self::$synch = Synchronizer::getInstance();
    if(Cache::getInstance() instanceof NullAdapter){
      throw new RuntimeException('Невозможно выполнить тест. Не используется реальная система кэширования.');
    }
  }

  /**
   * @covers Synchronizer::add
   */
  public function testAdd(){
    self::$synch->add('TestClass', 1, ['a' => 1, 'b' => 2]);
    $this->assertEquals(['a' => 1, 'b' => 2], self::$cache->get('Synchronizer_Pile_TestClass:1'));
    $this->assertEquals([0 => 1], self::$cache->get('Synchronizer_Classes_TestClass'));
    self::$synch->add('TestClass', 1, ['a' => 1, 'b' => 2]);
    $this->assertEquals(['a' => 1, 'b' => 2], self::$cache->get('Synchronizer_Pile_TestClass:1'));
    $this->assertEquals([0 => 1], self::$cache->get('Synchronizer_Classes_TestClass'));
    self::$cache->remove('Synchronizer_Pile_TestClass:1');
    self::$cache->remove('Synchronizer_Classes_TestClass');
  }

  /**
   * @covers Synchronizer::get
   */
  public function testGet(){
    self::$cache->set('Synchronizer_Pile_TestClass:1', ['a' => 1, 'b' => 2]);
    $this->assertEquals(['a' => 1, 'b' => 2], self::$synch->get('TestClass', 1));
    self::$cache->set('Synchronizer_Classes_TestClass', [0 => 1]);
    $this->assertEquals(['a' => 1, 'b' => 2], self::$synch->get('TestClass', 1));
    self::$cache->remove('Synchronizer_Pile_TestClass:1');
    self::$cache->remove('Synchronizer_Classes_TestClass');
  }

  /**
   * @covers Synchronizer::remove
   */
  public function testRemove(){
    self::$cache->set('Synchronizer_Pile_TestClass:1', ['a' => 1, 'b' => 2]);
    self::$cache->set('Synchronizer_Classes_TestClass', [0 => 1]);
    self::$synch->remove('TestClass', 1);
    $this->assertEquals([], self::$cache->get('Synchronizer_Classes_TestClass'));
    $this->assertEquals(null, self::$cache->get('Synchronizer_Pile_TestClass:1'));
    self::$cache->set('Synchronizer_Pile_TestClass:1', ['a' => 1, 'b' => 2]);
    self::$synch->remove('TestClass', 1);
    $this->assertEquals(null, self::$cache->get('Synchronizer_Pile_TestClass:1'));
    self::$cache->set('Synchronizer_Classes_TestClass', [0 => 1]);
    self::$synch->remove('TestClass', 1);
    $this->assertEquals([], self::$cache->get('Synchronizer_Classes_TestClass'));
    self::$cache->remove('Synchronizer_Pile_TestClass:1');
    self::$cache->remove('Synchronizer_Classes_TestClass');
  }

  /**
   * @covers Synchronizer::find
   */
  public function testFind(){
    self::$cache->set('Synchronizer_Pile_TestClass:1', ['a' => 1, 'b' => 2]);
    self::$cache->set('Synchronizer_Classes_TestClass', [0 => 1]);
    $this->assertEquals([1 => ['a' => 1, 'b' => 2]], self::$synch->find('TestClass', [['a', '=', 1]]));
    self::$cache->set('Synchronizer_Pile_TestClass:2', ['a' => 2, 'b' => 2]);
    self::$cache->set('Synchronizer_Classes_TestClass', [0 => 1, 1 => 2]);
    $this->assertEquals([1 => ['a' => 1, 'b' => 2]], self::$synch->find('TestClass', [['a', '=', 1]]));
    self::$cache->set('Synchronizer_Pile_TestClass:3', ['a' => 1, 'b' => 2]);
    self::$cache->set('Synchronizer_Classes_TestClass', [0 => 1, 1 => 2, 2 => 3]);
    $this->assertEquals([1 => ['a' => 1, 'b' => 2], 3 => ['a' => 1, 'b' => 2]], self::$synch->find('TestClass', [['a', '=', 1]]));
    self::$cache->remove('Synchronizer_Pile_TestClass:1');
    self::$cache->remove('Synchronizer_Classes_TestClass');
  }
}
