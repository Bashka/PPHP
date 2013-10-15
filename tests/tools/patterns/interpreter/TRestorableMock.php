<?php
namespace PPHP\tests\tools\patterns\interpreter;

use PPHP\tools\patterns\interpreter\RestorableAdapter;

class TRestorableMock extends RestorableAdapter{
  protected $vars = [];

  public static function getMasks(){
    return ['(' . self::getPatterns()['varName'] . '):(' . self::getPatterns()['varVal'] . ')', '(' . self::getPatterns()['varName'] . ') (' . self::getPatterns()['varVal'] . ')'];
  }

  public static function getPatterns(){
    return ['varName' => '[A-Za-z_][A-Za-z0-9_]*', 'varVal' => '[1-9][0-9]*'];
  }

  public static function updateString(&$string){
    $string = str_replace('*', ':', $string);
  }
}