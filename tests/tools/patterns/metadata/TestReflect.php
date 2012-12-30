<?php
namespace PPHP\tests\tools\patterns\metadata;
spl_autoload_register(function($className){
  $root = 'C:/WebServers/home/dic/www';
  require_once $root . '/' . str_replace('\\', '/', $className) . '.php';
});
/*
 * 5 свойств
 * 2 метода
 */
class TestParentReflect implements \PPHP\tools\patterns\metadata\reflection\Reflect{
use \PPHP\tools\patterns\metadata\reflection\TReflect;

  private $prop;

  protected $parentProp;

  private function method(){
  }

  protected function method2(){
  }
}

TestParentReflect::getReflectionClass()->setMetadata('Metadata', 'TestParent');

TestParentReflect::getReflectionProperty('prop')->setMetadata('Metadata', 'TestParent');

TestParentReflect::getReflectionProperty('parentProp')->setMetadata('Metadata', 'TestParent');

TestParentReflect::getReflectionMethod('method')->setMetadata('Metadata', 'TestParent');

/*
 * 7 свойств
 * 9 метода
 */
class TestReflect extends TestParentReflect{
  private $prop;

  protected $childProp;

  static private $propStatic;

  private function method(){
  }

  static private function methodStatic(){
  }
}

TestReflect::getReflectionClass()->setMetadata('Metadata', 'Test');

TestReflect::getReflectionProperty('prop')->setMetadata('Metadata', 'Test');

TestReflect::getReflectionProperty('propStatic')->setMetadata('Metadata', 'Test');

TestReflect::getReflectionMethod('method')->setMetadata('Metadata', 'Test');

TestReflect::getReflectionMethod('methodStatic')->setMetadata('Metadata', 'Test');