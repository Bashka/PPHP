<?php
namespace PPHP\tests\tools\patterns\metadata\reflection;

use PPHP\tools\patterns\metadata\reflection as reflection;

/**
 * Class ChildReflectMock
 * Описание класса.
 * @package PPHP\tests\tools\patterns\metadata\reflection
 */
class ChildReflectMock extends ParentReflectMock{
  private $e;

  protected $f;

  static private $g;

  private function h(){
  }

  static private function j(){
  }
}

ChildReflectMock::getReflectionClass()->setMetadata('Metadata', 'Test');
ChildReflectMock::getReflectionProperty('e')->setMetadata('Metadata', 'Test');
ChildReflectMock::getReflectionProperty('g')->setMetadata('Metadata', 'Test');
ChildReflectMock::getReflectionMethod('h')->setMetadata('Metadata', 'Test');
ChildReflectMock::getReflectionMethod('j')->setMetadata('Metadata', 'Test');