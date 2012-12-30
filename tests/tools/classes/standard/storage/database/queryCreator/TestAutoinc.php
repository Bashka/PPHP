<?php
namespace PPHP\tests\tools\classes\standard\storage\database\queryCreator;

class TestAutoinc extends \PPHP\services\database\identification\Autoincrement{
  protected $OID = 1;

  public function generateOID(){
    $OID = $this->OID;
    $this->OID++;
    return $OID;
  }

  public function resetOID(){
    $this->OID = 1;
  }

}
