<?php
namespace PPHP\tests\tools\patterns\cache;

use PPHP\tools\patterns\cache\Cache;

class CacheMock extends Cache{
  protected function getFromSource($key, array $arguments = null){
    return $key;
  }
}
