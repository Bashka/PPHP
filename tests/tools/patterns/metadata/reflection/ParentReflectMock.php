<?php
namespace PPHP\tests\tools\patterns\metadata\reflection;

use PPHP\tools\patterns\metadata\reflection as reflection;

/**
 * @testMetadata testValue
 * @testMarker
 */
class ParentReflectMock implements reflection\Reflect{
  use reflection\TReflect;

  /**
   * @testMetadata testValue
   * @testMarker
   */
  private $a;

  protected $b;

  /**
   * @testMetadata testValue
   * @testMarker
   */
  private function c($x){
  }

  protected function d(){
  }
}

ParentReflectMock::getReflectionClass()->setMetadata('Metadata', 'TestParent');
ParentReflectMock::getReflectionProperty('a')->setMetadata('Metadata', 'TestParent');
ParentReflectMock::getReflectionProperty('b')->setMetadata('Metadata', 'TestParent');
ParentReflectMock::getReflectionMethod('c')->setMetadata('Metadata', 'TestParent');