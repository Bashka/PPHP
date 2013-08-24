<?php
namespace PPHP\tests\services\formatting\localisation;

use PPHP\tools\patterns\metadata\reflection\Reflect;
use PPHP\tools\patterns\metadata\reflection\TReflect;

class TestLocaliseClass implements Reflect{
  use TReflect;

  public $message;
}
