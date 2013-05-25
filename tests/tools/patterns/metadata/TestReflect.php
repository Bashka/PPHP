<?php
namespace PPHP\tests\tools\patterns\metadata;
use \PPHP\tools\patterns\metadata\reflection as reflection;

class TestReflect extends ParentReflectMock{
  private $e;

  protected $f;

  static private $g;

  private function h(){
  }

  static private function j(){
  }
}

TestReflect::getReflectionClass()->setMetadata('Metadata', 'Test');

TestReflect::getReflectionProperty('e')->setMetadata('Metadata', 'Test');
TestReflect::getReflectionProperty('g')->setMetadata('Metadata', 'Test');
TestReflect::getReflectionMethod('h')->setMetadata('Metadata', 'Test');
TestReflect::getReflectionMethod('j')->setMetadata('Metadata', 'Test');