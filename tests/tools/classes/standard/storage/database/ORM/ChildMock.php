<?php
namespace PPHP\tests\tools\classes\standard\storage\database\ORM;

/**
 * @ORM\Table ChildTable
 * @ORM\PK OID
 */
class ChildMock extends ParentMock{
  /**
   * @ORM\ColumnName df
   */
  private $d = 4;
  /**
   * @ORM\ColumnName ef
   */
  protected $e = 5;
  /**
   * @ORM\ColumnName ff
   */
  public $f = 6;

  /**
   * @ORM\Assoc \PPHP\tests\tools\classes\standard\storage\database\ORM\ChildMock
   * @ORM\FK h
   */
  public $g;

  /**
   * @ORM\ColumnName hf
   */
  public $h;

  protected function getSavedState(){
    return get_object_vars($this) + parent::getSavedState();
  }

  protected function setSavedState(array $state){
    parent::setSavedState($state);

    foreach($state as $k => $v){
      if(property_exists($this, $k) && $this::getReflectionProperty($k)->getDeclaringClass()->getName() === get_class()){
        $this->$k = $state[$k];
      }
    }
  }

  /**
   * @return mixed
   */
  public function getD(){
    return $this->d;
  }

  /**
   * @return mixed
   */
  public function getE(){
    return $this->e;
  }
}