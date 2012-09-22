<?php
namespace PPHP\model\modules\System\Users\tests;

class MockDataMapper extends \PPHP\tools\classes\standard\storage\database\DataMapper{
  protected $OID = 1;
  protected $pass = 'pass';

  public function recoverFinding(\PPHP\tools\patterns\database\LongObject $object, array $requiredProperties){
    $OID = $requiredProperties['OID'];
    $pass = $requiredProperties['password'];
    if(!($this->OID === $OID && $pass === $this->pass)){
      throw new \PPHP\tools\classes\standard\storage\database\UncertaintyException;
    }
    $object->restoreFromMemento(new \PPHP\tools\patterns\memento\Memento($object, ['OID' => $this->OID, 'password' => $this->pass]));
    return true;
  }

  public function update(\PPHP\tools\patterns\database\LongObject $object){
    $properties = $object->createMemento()->getState($object);
    $this->OID = (isset($properties['OID'])? $properties['OID'] : null);
    $this->pass = (isset($properties['password'])? $properties['password'] : null);
  }

  public function insert(\PPHP\tools\patterns\database\LongObject $object){
    if($object->isOID()){
      return false;
    }
    $object->setOID(1);
    $this->update($object);
  }

  public function restart(){
    $this->OID = 1;
    $this->pass = 'pass';
  }

  public function getOID(){
    return $this->OID;
  }

  public function getPass(){
    return $this->pass;
  }
}