<?php
namespace PPHP\tests\tools\classes\standard\storage\database\ORM;
use PPHP\tools\patterns\database\LongObject;

/**
 * @ORM\Table ParentTable
 * @ORM\PK OID
 */
class ParentMock extends LongObject{
  /**
   * @ORM\ColumnName af
   */
  private $a = 1;
  protected $b = 2;
  public $c = 3;

  protected function getSavedState(){
    return get_object_vars($this);
  }

  protected function setSavedState(array $state){
    foreach($state as $k => $v){
      if(property_exists($this, $k) && $this::getReflectionProperty($k)->getDeclaringClass()->getName() === get_class()){
        $this->$k = $state[$k];
      }
    }
  }

  /**
   * @return mixed
   */
  public function getA(){
    return $this->a;
  }

  /**
   * @return int
   */
  public function getB(){
    return $this->b;
  }
}