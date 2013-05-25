<?php
namespace PPHP\tests\tools\classes\standard\storage\database\ORM;
use \PPHP\tools\classes\standard\storage\database\ORM\Insert;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';

class InsertTest extends \PHPUnit_Framework_TestCase {
  /**
   * @covers Insert::metamorphose
   */
  public function testMetamorphose(){
    $o = new ChildMock;
    $o->f = ChildMock::getProxy(1);
    $ds = Insert::metamorphose($o,2);
    $this->assertEquals('INSERT INTO `ChildTable` (`OID`,ChildTable.df,ChildTable.ef,ChildTable.ff,ChildTable.hf) VALUES ("2","4","5","$/PPHP/tests/tools/classes/standard/storage/database/ORM/ChildMock:1","")', $ds[0]->interpretation());
    $this->assertEquals('INSERT INTO `ParentTable` (`OID`,ParentTable.af) VALUES ("2","1")', $ds[1]->interpretation());
  }
}
