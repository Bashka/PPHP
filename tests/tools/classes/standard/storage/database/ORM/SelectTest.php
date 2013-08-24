<?php
namespace PPHP\tests\tools\classes\standard\storage\database\ORM;

use PPHP\tools\classes\standard\storage\database\ORM\Select;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class SelectTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers Select::metamorphose
   */
  public function testMetamorphose(){
    $o = ParentMock::getProxy(1);
    $this->assertEquals('SELECT ParentTable.af AS a,ParentTable.OID AS OID FROM `ParentTable`  WHERE (ParentTable.OID = "1")', Select::metamorphose($o)->interpretation());
    $o = ChildMock::getProxy(1);
    $this->assertEquals('SELECT ChildTable.df AS d,ChildTable.ef AS e,ChildTable.ff AS f,ChildTable.hf AS h,ParentTable.af AS a,ChildTable.OID AS OID FROM `ChildTable` INNER JOIN `ParentTable` ON (ChildTable.OID = ParentTable.OID) WHERE (ChildTable.OID = "1")', Select::metamorphose($o)->interpretation());
  }

  /**
   * @covers Select::metamorphoseAssociation
   */
  public function testMetamorphoseAssociation(){
    $this->assertEquals('SELECT ChildTable.df AS d,ChildTable.ef AS e,ChildTable.ff AS f,ChildTable.hf AS h,ParentTable.af AS a,ChildTable.OID AS OID FROM `ChildTable` INNER JOIN `ParentTable` ON (ChildTable.OID = ParentTable.OID)', Select::metamorphoseAssociation(ChildMock::getReflectionClass())->interpretation());
    $this->assertEquals('SELECT ChildTable.df AS d,ChildTable.ef AS e,ChildTable.ff AS f,ChildTable.hf AS h,ParentTable.af AS a,ChildTable.OID AS OID FROM `ChildTable` INNER JOIN `ParentTable` ON (ChildTable.OID = ParentTable.OID) WHERE (ParentTable.af = "1")', Select::metamorphoseAssociation(ChildMock::getReflectionClass(), [['a', '=', '1']])->interpretation());
    $this->assertEquals('SELECT ChildTable.df AS d,ChildTable.ef AS e,ChildTable.ff AS f,ChildTable.hf AS h,ParentTable.af AS a,ChildTable.OID AS OID FROM `ChildTable` INNER JOIN `ParentTable` ON (ChildTable.OID = ParentTable.OID) WHERE ((ParentTable.af = "$/PPHP/tests/tools/classes/standard/storage/database/ORM/ParentMock:1") AND (ChildTable.df > "0"))', Select::metamorphoseAssociation(ChildMock::getReflectionClass(), [['a', '=', ParentMock::getProxy(1)], ['d', '>', 0]])->interpretation());
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException');
    Select::metamorphoseAssociation(ChildMock::getReflectionClass(), [['j', '=', '1']]);
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\InvalidArgumentException');
    Select::metamorphoseAssociation(ChildMock::getReflectionClass(), [['h', '!', '1']]);
  }
}
