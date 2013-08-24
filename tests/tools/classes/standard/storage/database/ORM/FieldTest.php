<?php
namespace PPHP\tests\tools\classes\standard\storage\database\ORM;

use PPHP\tools\classes\standard\storage\database\ORM\Field;

spl_autoload_register(function ($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class FieldTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers Field::metamorphose
   */
  public function testMetamorphose(){
    $this->assertEquals('ParentTable.af', Field::metamorphose(ParentMock::getReflectionClass(), 'a')->interpretation());
    $this->assertEquals('ParentTable.OID', Field::metamorphose(ParentMock::getReflectionClass(), 'OID')->interpretation());
    $this->assertEquals('ChildTable.df', Field::metamorphose(ChildMock::getReflectionClass(), 'd')->interpretation());
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException');
    Field::metamorphose(ParentMock::getReflectionClass(), 'c');
    $this->setExpectedException('\PPHP\tools\classes\standard\baseType\exceptions\NotFoundDataException');
    Field::metamorphose(ParentMock::getReflectionClass(), 'd');
  }
}
