<?php
namespace PPHP\tests\tools\classes\standard\storage\database\ORM;

  // Сущности класса записываются в таблицу ChildTable и проецируют свойство OID на поле OID
/**
 * @ORM\Table ChildTable
 * @ORM\PK OID
 */
class ChildMock extends ParentMock{
  // Свойство проецируется на поле df таблицы класса
  /**
   * @ORM\ColumnName df
   */
  private $d = 4;
  // Свойство проецируется на поле ef таблицы класса
  /**
   * @ORM\ColumnName ef
   */
  protected $e = 5;
  // Свойство проецируется на поле ff таблицы класса
  /**
   * @ORM\ColumnName ff
   */
  public $f = 6;
  // Свойство представляет множественную ассоциацию с классом ChildMock на основании его свойства h. Ассоциация композитна и загружается сразу
  /**
   * @ORM\Assoc \PPHP\tests\tools\classes\standard\storage\database\ORM\ChildMock
   * @ORM\FK h
   * @ORM\Composition
   * @ORM\Full
   */
  public $g;
  // Свойство проецируется на поле hf таблицы класса
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