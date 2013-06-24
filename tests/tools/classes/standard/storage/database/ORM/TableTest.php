<?php
namespace PPHP\tests\tools\classes\standard\storage\database\ORM;

use PPHP\tools\classes\standard\storage\database\ORM\Table;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class TableTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers Table::metamorphose
   */
  public function testMetamorphose(){
    $this->assertEquals('ParentTable', Table::metamorphose(ParentMock::getReflectionClass())->interpretation());
    $this->assertEquals('ChildTable', Table::metamorphose(ChildMock::getReflectionClass())->interpretation());
  }
}
