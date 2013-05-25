<?php
namespace PPHP\tests\tools\classes\standard\baseType\special\network;
use PPHP\tools\classes\standard\baseType\special\network\Report;
spl_autoload_register(function($className){
  require_once $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $className) . '.php';
});
$_SERVER['DOCUMENT_ROOT'] = '/var/www';
class ReportTest extends \PHPUnit_Framework_TestCase{
  /**
   * @covers Report::isReestablish
   */
  public function testIsReestablish(){
    $this->assertFalse(Report::isReestablish(''));
    $this->assertTrue(Report::isReestablish('http://'));
    $this->assertTrue(Report::isReestablish('ftp://'));
    $this->assertFalse(Report::isReestablish('http:/'));
    $this->assertFalse(Report::isReestablish('http//'));
    $this->assertFalse(Report::isReestablish('http'));
    $this->assertFalse(Report::isReestablish('://'));
    $this->assertFalse(Report::isReestablish('http:'));
    $this->assertFalse(Report::isReestablish('test://'));
  }

  /**
   * @covers Report::reestablish
   * @covers Report::getName
   */
  public function testReestablish(){
    $o = Report::reestablish('http://');
    $this->assertEquals('http', $o->getName());
  }
}
