<?php
namespace PPHP\tests\tools\patterns\metadata;
use \PPHP\tools\patterns\metadata\reflection as reflection;

/**
 * @testMetadata testValue
 */
class ParentReflectMock implements reflection\Reflect{
  use reflection\TReflect;

  /**
   * @testMetadata testValue
   */
  private $a;

  protected $b;

  /**
   * @testMetadata testValue
   */
  private function c(){
  }

  protected function d(){
  }
}

ParentReflectMock::getReflectionClass()->setMetadata('Metadata', 'TestParent');

ParentReflectMock::getReflectionProperty('a')->setMetadata('Metadata', 'TestParent');
ParentReflectMock::getReflectionProperty('b')->setMetadata('Metadata', 'TestParent');
ParentReflectMock::getReflectionMethod('c')->setMetadata('Metadata', 'TestParent');