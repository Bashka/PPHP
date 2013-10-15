<?php
namespace PPHP\tests\tools\patterns\cache;

use PPHP\tools\patterns\cache\Cache;

class CacheMock extends Cache{
  public static $data = ['key' => 1];

  protected function getFromSource($key, array $arguments = null){
    if(!array_key_exists($key, self::$data)){
      return null;
    }

    return self::$data[$key];
  }
}
