<?php
namespace PPHP\model\modules\System\Users\Access;

/**
 * Представление роли (совокупности правил).
 */
class Role extends \PPHP\tools\patterns\database\LongObject implements \JsonSerializable{
  /**
   * Наименование роли.
   * @var string
   */
  protected $name;

  /**
   * Множество правил доступа, связанных с данной ролью.
   * @var \PPHP\tools\patterns\database\associations\LongAssociation
   */
  protected $rules;

  public function JsonSerialize(){
    return ['OID' => $this->getOID(), 'name' => $this->name, 'rules' => $this->rules];
  }

  protected function getSavedState(){
    return get_object_vars($this);
  }

  /**
   * @return string
   */
  public function getName(){
    return $this->name;
  }

  /**
   * @return \PPHP\tools\patterns\database\associations\LongAssociation
   */
  public function getRules(){
    return $this->rules;
  }

  /**
   * @param string $name
   */
  public function setName($name){
    $this->name = $name;
  }
}
Role::getReflectionClass()->setMetadata('NameTable', 'Access_Roles');
Role::getReflectionClass()->setMetadata('KeyTable', 'OID');

Role::getReflectionProperty('name')->setMetadata('NameFieldTable', 'name');
Role::getReflectionProperty('rules')->setMetadata('AssocClass', 'PPHP\model\modules\System\Users\Access\LinkageRoleRule');
Role::getReflectionProperty('rules')->setMetadata('KeyAssocTable', 'role');