<?php
namespace PPHP\tests\tools\classes\standard\storage\database\ORM;
use PPHP\tools\classes\standard\storage\database\ORM\Join;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';

class JoinTest extends \PHPUnit_Framework_TestCase {
  /**
   * @covers Join::metamorphose
   */
  public function testMetamorphose(){
    $this->assertEquals('INNER JOIN `ParentTable` ON (ChildTable.OID = ParentTable.OID)', Join::metamorphose(ChildMock::getReflectionClass(), ParentMock::getReflectionClass())->interpretation());
  }

  /**
   * @covers Join::getPKField
   */
  public function testGetPKField(){
    $this->assertEquals('`OID`', Join::getPKField(ParentMock::getReflectionClass())->interpretation());
  }
}
