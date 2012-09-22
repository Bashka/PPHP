<?php
namespace PPHP\model\modules\System\Users\Access;

class LinkageUserRole extends \PPHP\tools\patterns\database\LongObject implements \JsonSerializable{
  protected $user;

  protected $role;

  public function JsonSerialize(){
    return ['OID' => $this->getOID(), 'user' => $this->user, 'role' => $this->role];
  }

  protected function getSavedState(){
    return get_object_vars($this);
  }

  public function getRole(){
    return $this->role;
  }

  public function setRole($role){
    $this->role = $role;
  }

  public function setUser($user){
    $this->user = $user;
  }

  public function getUser(){
    return $this->user;
  }
}

LinkageUserRole::getReflectionClass()->setMetadata('NameTable', 'Access_UserRole');
LinkageUserRole::getReflectionClass()->setMetadata('KeyTable', 'OID');

LinkageUserRole::getReflectionProperty('role')->setMetadata('NameFieldTable', 'role');
LinkageUserRole::getReflectionProperty('user')->setMetadata('NameFieldTable', 'user');