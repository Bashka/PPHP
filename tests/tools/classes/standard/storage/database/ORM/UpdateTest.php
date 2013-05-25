<?php
namespace PPHP\tests\tools\classes\standard\storage\database\ORM;
use \PPHP\tools\classes\standard\storage\database\ORM\Update;
use PPHP\tools\patterns\database\query\Where;
use PPHP\tools\patterns\database\query\Field;
use PPHP\tools\patterns\database\query\LogicOperation;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';

class UpdateTest extends \PHPUnit_Framework_TestCase {
  /**
   * @covers Insert::metamorphose
   */
  public function testMetamorphose(){
    $o = ChildMock::getProxy(1);
    $o->f = ChildMock::getProxy(2);
    $ds = Update::metamorphose($o);
    $this->assertEquals('UPDATE `ChildTable` SET ChildTable.df = "4",ChildTable.ef = "5",ChildTable.ff = "$/PPHP/tests/tools/classes/standard/storage/database/ORM/ChildMock:2",ChildTable.hf = "" WHERE (`OID` = "1")', $ds[0]->interpretation());
    $this->assertEquals('UPDATE `ParentTable` SET ParentTable.af = "1" WHERE (`OID` = "1")', $ds[1]->interpretation());

    $o = ChildMock::getProxy(1);
    $o->f = ChildMock::getProxy(2);
    $ds = Update::metamorphose($o, new Where(new LogicOperation(new Field('af'), '>', 0)));
    $this->assertEquals('UPDATE `ChildTable` SET ChildTable.df = "4",ChildTable.ef = "5",ChildTable.ff = "$/PPHP/tests/tools/classes/standard/storage/database/ORM/ChildMock:2",ChildTable.hf = "" WHERE (`af` > "0")', $ds[0]->interpretation());
    $this->assertEquals('UPDATE `ParentTable` SET ParentTable.af = "1" WHERE (`af` > "0")', $ds[1]->interpretation());
  }
}
