<?php
namespace PPHP\tests\tools\classes\standard\storage\database\ORM;

use PPHP\tools\classes\standard\storage\database\ORM\Delete;
use PPHP\tools\patterns\database\query\Field;
use PPHP\tools\patterns\database\query\LogicOperation;
use PPHP\tools\patterns\database\query\Where;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class DeleteTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers Delete::metamorphose
   */
  public function testMetamorphose(){
    $ds = Delete::metamorphose(ChildMock::getProxy(1));
    $this->assertEquals('DELETE FROM `ChildTable` WHERE (`OID` = "1")', $ds[0]->interpretation());
    $this->assertEquals('DELETE FROM `ParentTable` WHERE (`OID` = "1")', $ds[1]->interpretation());
    $ds = Delete::metamorphose(ChildMock::getProxy(1), new Where(new LogicOperation(new Field('af'), '>', 0)));
    $this->assertEquals('DELETE FROM `ChildTable` WHERE (`af` > "0")', $ds[0]->interpretation());
    $this->assertEquals('DELETE FROM `ParentTable` WHERE (`af` > "0")', $ds[1]->interpretation());
  }
}
