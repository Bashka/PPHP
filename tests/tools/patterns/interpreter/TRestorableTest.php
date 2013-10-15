<?php
namespace PPHP\tests\tools\patterns\interpreter;

use PPHP\tests\tools\patterns\interpreter\TRestorableMock;

require_once substr(__DIR__, 0, strpos(__DIR__, 'PPHP')) . 'PPHP/dev/autoload/autoload.php';
class TRestorableTest extends \PHPUnit_Framework_TestCase{
  /**
   * Должен возвращать массив найденных лексем.
   * @covers \PPHP\tools\patterns\interpreter\TRestorable::reestablish
   */
  public function testShouldReturnFoundTokensArray(){
    $tokens = TRestorableMock::reestablish('a:1');
    $this->assertEquals('a:1', $tokens[0]);
    $this->assertEquals('a', $tokens[1]);
    $this->assertEquals('1', $tokens[2]);
  }

  /**
   * Элемент в массиве лексем с ключем key должен содержать индекс подходящего шаблона верификации.
   * @covers \PPHP\tools\patterns\interpreter\TRestorable::reestablish
   */
  public function testElementKeyIndexMustContainTemplateIndex(){
    $tokens = TRestorableMock::reestablish('a:1');
    $this->assertEquals(0, $tokens['key']);
    $tokens = TRestorableMock::reestablish('a 1');
    $this->assertEquals(1, $tokens['key']);
  }

  /**
   * Должен выбрасывать исключение при отсутствии подходящего шаблона верификации.
   * @covers \PPHP\tools\patterns\interpreter\TRestorable::reestablish
   */
  public function testShouldThrowExceptionIfNotFoundTemplate(){
    $this->setExpectedException('PPHP\tools\classes\standard\baseType\exceptions\StructureException');
    TRestorableMock::reestablish('a');
  }

  /**
   * Должен применять метод updateString перед поиском.
   * @covers \PPHP\tools\patterns\interpreter\TRestorable::reestablish
   */
  public function testShouldCallUpdateStringMethodBeforeSearching(){
    $tokens = TRestorableMock::reestablish('a*1');
    $this->assertEquals(0, $tokens['key']);
  }

  /**
   * Должен возвращать true для тех строк, для которых найден хотя бы один шаблон верификации.
   * @covers \PPHP\tools\patterns\interpreter\TRestorable::isReestablish
   */
  public function testShouldReturnTrueForSuitableString(){
    $this->assertTrue(TRestorableMock::isReestablish('a:1'));
    $this->assertTrue(TRestorableMock::isReestablish('a 1'));
  }

  /**
   * Должен возвращать false для тех строк, для которых не найдено ни одного шаблона верификации.
   * @covers \PPHP\tools\patterns\interpreter\TRestorable::isReestablish
   */
  public function testShouldReturnFalseForNoSuitableString(){
    $this->assertFalse(TRestorableMock::isReestablish('a-1'));
  }
}
