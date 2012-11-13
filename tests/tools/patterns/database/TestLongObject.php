<?php
namespace PPHP\tests\tools\patterns\database;

class TestParentObject extends \PPHP\tools\patterns\database\LongObject{
  private $parentPrivProp = 1;
  protected $parentProtProp = 2;
  public $parentPublProp = 3;

  protected function getSavedState(){
    return get_object_vars($this);
  }
}

TestParentObject::getReflectionClass()->setMetadata('NameTable', 'ParentTable');
TestParentObject::getReflectionClass()->setMetadata('KeyTable', 'Key');

TestParentObject::getReflectionProperty('parentPrivProp')->setMetadata('NameFieldTable', 'parentPrivField');
TestParentObject::getReflectionProperty('parentProtProp')->setMetadata('NameFieldTable', 'parentProtField');
TestParentObject::getReflectionProperty('parentPublProp')->setMetadata('NameFieldTable', 'parentPublField');

class TestLongObject extends TestParentObject{
  private $privProp = 1;
  protected $protProp = 2;
  public $publProp;
  public $linkProp;
  static public $staticProp = 'static prop';

  protected function getSavedState(){
    return get_object_vars($this);
  }

  protected function setSavedState(array $state){
    foreach($state as $k => $v){
      $this->$k = $v;
    }
  }

  public function setLinkProp($linkProp){
    $this->linkProp = $linkProp;
  }

  public function getLinkProp(){
    return $this->linkProp;
  }

  public function setPrivProp($privProp){
    $this->privProp = $privProp;
  }

  public function getPrivProp(){
    return $this->privProp;
  }

  public function setProtProp($protProp){
    $this->protProp = $protProp;
  }

  public function getProtProp(){
    return $this->protProp;
  }

  public function setPublProp($publProp){
    $this->publProp = $publProp;
  }

  public function getPublProp(){
    return $this->publProp;
  }

  public static function setStaticProp($staticProp){
    self::$staticProp = $staticProp;
  }

  public static function getStaticProp(){
    return self::$staticProp;
  }
}

TestLongObject::getReflectionClass()->setMetadata('NameTable', 'ChildTable');
TestLongObject::getReflectionClass()->setMetadata('KeyTable', 'Key');

TestLongObject::getReflectionProperty('privProp')->setMetadata('NameFieldTable', 'privField');
TestLongObject::getReflectionProperty('protProp')->setMetadata('NameFieldTable', 'protField');
TestLongObject::getReflectionProperty('linkProp')->setMetadata('NameFieldTable', 'linkField');

TestLongObject::getReflectionProperty('publProp')->setMetadata('AssocClass', '\PPHP\tests\tools\patterns\database\TestAssoc');
TestLongObject::getReflectionProperty('publProp')->setMetadata('KeyAssocTable', 'ChildTable');