<?php
namespace PPHP\tests\tools\patterns\interpreter;

use PPHP\tools\patterns\interpreter\Restorable;
use PPHP\tools\patterns\interpreter\TRestorable;

class TRestorebleParentMock implements Restorable{
  use TRestorable;
}

class TRestorableMock extends TRestorebleParentMock{
  protected $vars = [];

  public static function getMasks(){
    return ['(' . self::getPatterns()['varName'] . '):(' . self::getPatterns()['varVal'] . ')'];
  }

  public static function getPatterns(){
    return ['varName' => '[A-Za-z_][A-Za-z0-9_]*', 'varVal' => '[1-9][0-9]*'];
  }

  public static function reestablish($string, $driver = null){
    // Контроль типа и верификация выполняется в вызываемом родительском методе.
    $m = parent::reestablish($string, $driver);
    $o = new static;
    $o->addVar($m[1], $m[2]);

    return $o;
  }

  public function addVar($key, $val){
    $this->vars[$key] = $val;
  }

  public function getVar($key){
    return $this->vars[$key];
  }
}